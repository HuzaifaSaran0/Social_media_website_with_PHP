<?php
ob_start();
session_start();

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            $_SESSION['email'] = $email; // Store user email in session
            $_SESSION['user_id'] = $user['user_id']; // Add this line
            header("Location: /testing/home.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password!";
        }
    } else {
        $_SESSION['error'] = "Invalid email or password!";
    }

    // Store entered email to pre-fill the form
    $_SESSION['entered_email'] = $email;

    // Redirect back to login page
    header("Location: /testing/login.php");
    exit();
}

// Close database connection
$conn->close();
ob_end_flush();
?>
