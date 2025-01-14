<?php
session_start();

header('Content-Type: application/json'); // Ensure JSON is always returned

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON received.']);
        exit();
    }

    $post_id = intval($data['post_id'] ?? 0);
    $action = $data['action'] ?? ''; // 'like' or 'dislike'
    $user_id = intval($_SESSION['user_id']);

    if ($post_id <= 0 || !in_array($action, ['like', 'dislike'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit();
    }

    $is_liked = ($action === 'like') ? 1 : 0;

    try {
        // Check if the user already reacted to the post
        $check_sql = "SELECT is_liked FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ii", $user_id, $post_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['is_liked'] == $is_liked) {
                // User clicked the same reaction again, so remove it (neutral state)
                $delete_sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
                $stmt = $conn->prepare($delete_sql);
                $stmt->bind_param("ii", $user_id, $post_id);
                $stmt->execute();
            } else {
                // User switched reaction (e.g., from like to dislike)
                $update_sql = "UPDATE likes SET is_liked = ? WHERE user_id = ? AND post_id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("iii", $is_liked, $user_id, $post_id);
                $stmt->execute();
            }
        } else {
            // Insert new reaction
            $insert_sql = "INSERT INTO likes (user_id, post_id, is_liked) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iii", $user_id, $post_id, $is_liked);
            $stmt->execute();
        }

        // Add notification
        $notification_sql = "INSERT INTO notifications (recipient_id, sender_id, post_id, action) 
                             SELECT user_id, ?, ?, ? FROM posts WHERE post_id = ?";
        $notification_action = $action;
        $stmt = $conn->prepare($notification_sql);
        $stmt->bind_param("iisi", $user_id, $post_id, $notification_action, $post_id);
        $stmt->execute();

        // Fetch updated like and dislike counts
        $likes_sql = "SELECT COUNT(*) AS likes_count FROM likes WHERE post_id = ? AND is_liked = 1";
        $stmt = $conn->prepare($likes_sql);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $likes_result = $stmt->get_result()->fetch_assoc();

        $dislikes_sql = "SELECT COUNT(*) AS dislikes_count FROM likes WHERE post_id = ? AND is_liked = 0";
        $stmt = $conn->prepare($dislikes_sql);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $dislikes_result = $stmt->get_result()->fetch_assoc();

        echo json_encode([
            'success' => true,
            'message' => 'Reaction recorded successfully.',
            'likes' => $likes_result['likes_count'] ?? 0,
            'dislikes' => $dislikes_result['dislikes_count'] ?? 0,
            'liked' => ($action === 'like'),
            'disliked' => ($action === 'dislike'),
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error processing request: ' . $e->getMessage()]);
    }
}

$conn->close();
?>
