<?php
include('../includes/db.php');  // Connect to the database
session_start();

if (isset($_POST['add_post'])) {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO posts (user_id, content, created_at) VALUES ('$user_id', '$content', NOW())";
    if ($conn->query($sql) === TRUE) {
        header("Location: ../views/home.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
