<?php
session_start();

// Debugging: Check current session variables
// echo "<pre>Session at start of login.php:</pre>";
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

// Handle error and entered_email session variables
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
$entered_email = isset($_SESSION['entered_email']) ? $_SESSION['entered_email'] : '';
unset($_SESSION['error'], $_SESSION['entered_email']); // Clear these session variables
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        /* Main container for split screen */
        .main-container {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
            flex-direction: row;
        }

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

        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .login-box h2 {
            margin-bottom: 1.5rem;
            text-align: center;
            color: #333;
        }

        .alert {
            text-align: center;
        }

        .btn-primary {
            background-color: #ff6e40;
            border: none;
        }
        .btn-primary:hover {
            background-color: #ff3d00;
        }

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
        <div class="left-panel">
            <h1>Come in!</h1>
        </div>
        <div class="right-panel">
            <div class="login-box">
                <h2>Login</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <form action="/testing/login_user.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email" 
                            value="<?php echo !empty($entered_email) ? htmlspecialchars($entered_email) : ''; ?>" 
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <p>Don't have an account? <a href="register.php">Register</a> now</p>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
