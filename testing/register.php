    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
        <style>
            /* Main container for split screen */
            .main-container {
                display: flex;
                min-height: 100vh;
                background-color: #f8f9fa;
                flex-direction: row;
            }

            /* Left side with curved rainbow design */
            .left-panel {
                flex: 1;
                background: linear-gradient(135deg, #ff8a80, #ff6e40, #ff3d00, #ff9100, #ffc400);
                border-top-right-radius: 50% 20%;
                border-bottom-right-radius: 50% 20%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
                color: #fff;
                text-align: center;
            }
            .left-panel h1 {
                font-size: 2.5rem;
                font-weight: bold;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            }

            /* Right side for registration form */
            .right-panel {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
            }

            /* Styling for registration box */
            .register-box {
                width: 100%;
                max-width: 400px;
                padding: 2rem;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            }
            .register-box h2 {
                margin-bottom: 1.5rem;
                text-align: center;
                color: #333;
            }

            /* Styling for button */
            .btn-primary {
                background-color: #ff6e40;
                border: none;
            }
            .btn-primary:hover {
                background-color: #ff3d00;
            }

            /* Mobile view adjustments */
            @media (max-width: 768px) {
                .main-container {
                    flex-direction: column;
                }
                .left-panel {
                    border-radius: 50% 50% 0 0;
                    padding: 3rem 2rem;
                }
                .left-panel h1 {
                    font-size: 2rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="main-container">
            <!-- Left panel with rainbow curve -->
            <div class="left-panel">
                <h1>Join Ali now</h1>
            </div>

            <!-- Right panel with registration form -->
            <div class="right-panel">
                <div class="register-box">
                    <!-- Display success or error message if exists -->
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
                    if (isset($_SESSION['success'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                        unset($_SESSION['success']);
                    }
                    ?>
                    <h2>Register</h2>
                    <form action="register_user.php" method="POST" onsubmit="return validateForm()">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                        <label for="profile_picture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                        <p>Already have account? <a href="login.php">Login</a> now</p>

                    </form>
                </div>
            </div>
        </div>

        <script>
            function validateForm() {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== confirmPassword) {
                    alert("Passwords do not match!");
                    return false; // Prevent form submission
                }
                return true; // Allow form submission
            }
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
