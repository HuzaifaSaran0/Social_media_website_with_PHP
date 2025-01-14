<?php
session_start();
// Add these lines at the top of load_comments.php and submit_comment.php
// ini_set('display_errors', 0); // Disable error display
// ini_set('log_errors', 1); // Enable error logging
// ini_set('error_log', __DIR__ . '/php-error.log'); // Log errors to a file


if (!isset($_SESSION['email']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'];
    $commentText = $_POST['comment_text'];
    $userId = $_SESSION['user_id']; // Get user ID from session
    $created_at = date('Y-m-d H:i:s');

    if (!empty($commentText)) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text, comment_created_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $postId, $userId, $commentText, $created_at);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
    }
}
$conn->close();
?>
