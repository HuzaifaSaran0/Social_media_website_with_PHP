<?php
session_start();
ob_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        ob_end_clean();
        header("Location: register.php");
        exit();
    }

    // Check if email already exists
    $check_email_query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $check_email_query);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error'] = "Email already exists!";
        ob_end_clean();
        header("Location: register.php");
        exit();
    }

    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $image_data = file_get_contents($_FILES['profile_picture']['tmp_name']);
        $profile_picture = mysqli_real_escape_string($conn, $image_data);
    }

    // Insert user into the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_query = "INSERT INTO users (username, email, password, profile_picture) VALUES ('$username', '$email', '$hashed_password', '$profile_picture')";

    if (mysqli_query($conn, $insert_query)) {
        $_SESSION['success'] = "Registration successful! Please login.";
        ob_end_clean();
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        ob_end_clean();
        header("Location: register.php");
        exit();
    }
} else {
    ob_end_clean();
    header("Location: register.php");
    exit();
}


?>