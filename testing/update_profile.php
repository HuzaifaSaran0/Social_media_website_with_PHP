<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialdb";  // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user data based on the logged-in user's email
$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);
$user_info = $result->fetch_assoc();

// Initialize variables for user information
$name = $user_info['username'];
$current_email = $user_info['email'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = $_POST['username'];
    $new_email = $_POST['email'];
    $password = $_POST['password'];

    // Verify password
    if (password_verify($password, $user_info['password'])) { // Assuming passwords are hashed
        // Update profile picture if a new one is uploaded
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            $image = file_get_contents($_FILES['profile_picture']['tmp_name']);
        } else {
            $image = $user_info['profile_picture']; // Keep the old picture if no new one is uploaded
        }

        // Update user information in the database
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE email = ?");
        $stmt->bind_param("ssss", $new_name, $new_email, $image, $email);

        if ($stmt->execute()) {
            $_SESSION['email'] = $new_email; // Update session email if the email was changed
            echo "<script>alert('Profile updated successfully'); window.location.href = 'profile_page.php';</script>";
        } else {
            echo "<script>alert('Error updating profile: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Incorrect password. Please try again.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Update Your Profile</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($current_email); ?>" required>
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (For Verification)</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
