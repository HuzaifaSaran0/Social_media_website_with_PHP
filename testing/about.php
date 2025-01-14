<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ALI</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color:rgb(228, 226, 225);
        }
        .navbar-brand {
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 1.5rem;
            color: #ff6e40 !important;
        }
        .navbar {
            background-color:rgb(228, 226, 225);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .welcome-banner {
            background: linear-gradient(135deg, #ff6e40, #ff9100);
            color: white;
            padding: 3rem 1rem;
            text-align: center;
        }
        .welcome-banner h1 {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .about-content {
            padding: 2rem 1rem;
            text-align: center;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .about-content h2 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        .about-content p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'nav.php'; ?>

    <!-- About Us Banner -->
    <div class="welcome-banner">
        <h1>About ALI</h1>
        <p>Your go-to platform for staying connected and sharing moments.</p>
    </div>

    <!-- About Us Content -->
    <div class="container about-content">
        <h2>Who We Are</h2>
        <p>
            Welcome to ALI, the social media platform where you can connect with friends, share your moments, and stay updated with the latest trends. 
            Our mission is to bring people together and create a space where everyone can express themselves freely and safely.
        </p>
        <p>
            At ALI, we believe in the power of community and the importance of staying connected. Whether you're sharing photos, videos, or just a quick status update, 
            ALI is here to help you stay in touch with the people who matter most to you.
        </p>
        <p>
            Thank you for being a part of our community. We are constantly working to improve your experience and bring you new features that make connecting with others 
            even easier and more enjoyable.
        </p>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> ALI. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
