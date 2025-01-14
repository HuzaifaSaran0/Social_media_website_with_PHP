<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// include 'db.php';    

header('Content-Type: application/json');
ob_start(); // Start output buffering

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $postId = $_GET['post_id'];

    if (!filter_var($postId, FILTER_VALIDATE_INT)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
        exit();
    }

    // Query preparation and execution
    $stmt = $conn->prepare("SELECT c.comment_text, c.comment_created_at, u.username 
    FROM comments c 
    JOIN users u ON c.user_id = u.user_id  -- Use the correct column name here
    WHERE c.post_id = ? 
    ORDER BY c.comment_created_at DESC");

    
    $stmt->bind_param("i", $postId);

    if (!$stmt->execute()) {
        // ob_clean();
        echo json_encode(['success' => false, 'message' => 'Database query failed']);
        exit();
    }

    // Fetch comments
    $result = $stmt->get_result();
    $comments = [];
    
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    // ob_clean();
    echo json_encode($comments);
    exit();
} else {
    // ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$conn->close();
?>
