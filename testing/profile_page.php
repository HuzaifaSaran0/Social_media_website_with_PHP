<?php
session_start();  // Ensure session_start is at the very beginning

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user data based on the logged-in user's email
$email = $_SESSION['email'];
$sql = $conn->prepare("SELECT * FROM users WHERE email = ?");
$sql->bind_param("s", $email);
$sql->execute();
$result = $sql->get_result();
$user_info = $result->fetch_assoc();

// Check if user data is found
$name = $user_info['username'] ?? 'User Name';
$email = $user_info['email'] ?? 'No Email Available';

// Fetch posts by the user
$post_result = [];
if (isset($user_info['user_id'])) {
    $sql_posts = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
    $sql_posts->bind_param("i", $user_info['user_id']);
    $sql_posts->execute();
    $post_result_query = $sql_posts->get_result();
    while ($row = $post_result_query->fetch_assoc()) {
        $post_result[] = $row;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $post_id = intval($_POST['post_id']); // Securely retrieve post_id

    // Verify the post belongs to the logged-in user
    $sql_verify = $conn->prepare("SELECT * FROM posts WHERE post_id = ? AND user_id = ?");
    $sql_verify->bind_param("ii", $post_id, $user_info['user_id']);
    $sql_verify->execute();
    $verify_result = $sql_verify->get_result();

    if ($verify_result->num_rows > 0) {
        // Delete the post
        $sql_delete = $conn->prepare("DELETE FROM posts WHERE post_id = ?");
        $sql_delete->bind_param("i", $post_id);

        if ($sql_delete->execute()) {
            $_SESSION['delete_success'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<script>alert('Failed to delete the post.');</script>";
        }
    } else {
        echo "<script>alert('Unauthorized attempt to delete post.');</script>";
    }
}



// Handle form submission for new posts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post'])) {
    $content = $_POST['content'];
    $media_type = null;
    $media_path = null;

    if (!empty($_FILES['media']['name'])) {
        $file = $_FILES['media'];
        $target_dir = "uploads/";
        $file_name = basename($file['name']);
        $target_file = $target_dir . uniqid() . "_" . $file_name;

        // Determine media type
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $media_type = 'image';
        } elseif (in_array($file_type, ['mp4', 'avi', 'mov', 'wmv'])) {
            $media_type = 'video';
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $target_file)) {
            echo "<script>alert('Failed to upload media file.');</script>";
        } else {
            $media_path = $target_file;
        }
    }

    if (!empty($content)) {
        $user_id = $user_info['user_id'];
        $created_at = date('Y-m-d H:i:s');

        // Insert post into database
        $sql_insert_post = $conn->prepare("INSERT INTO posts (user_id, content, media, media_type, created_at) VALUES (?, ?, ?, ?, ?)");
        $sql_insert_post->bind_param("issss", $user_id, $content, $media_path, $media_type, $created_at);
        if ($sql_insert_post->execute()) {
            $_SESSION['post_success'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "<script>alert('Please fill in content');</script>";
    }
}

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Beautiful Design</title>
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
        .profile-header {
            text-align: center;
            background: linear-gradient(135deg, #ff6e40, #ff9100);
            color: white;
            padding: 3rem 1rem;
            border-radius: 0 0 30px 30px;
        }
        .profile-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid white;
            cursor: pointer; /* Add cursor to indicate it's clickable */
        }
        .profile-header h1 {
            margin-top: 1rem;
            font-size: 2.5rem;
        }
        .profile-header p {
            font-size: 1.2rem;
        }
        .posts-section {
            margin-top: 2rem;
        }
        .post-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            padding: 1.5rem;
            transition: transform 0.2s;
        }
        .post-card:hover {
            transform: translateY(-5px);
        }
        .post-card small {
            color: #888;
        }

        /* Styling for modal */
        .modal-dialog {
            max-width: 30%;
        }
        .modal-body img {
            width: 100%;
            height: auto;
        }
        .toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #28a745;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    opacity: 1;
    transition: opacity 0.5s ease-out;
    z-index: 9999;
}
.post-card img,
    .post-card video {
        width: 100%;
        height: 200px; /* Standardize height */
        object-fit: cover; /* Ensure proportional resizing without distortion */
        border-radius: 8px;
        cursor: pointer; /* Indicate clickable media */

    }

    .modal-content img,
    .modal-content video {
        max-width: 100%;
        max-height: 100%;
        margin: auto;
        display: block;
    }
    @media (max-width: 576px) {
    .modal-dialog {
        max-width: 100%; /* Full width */
        margin: 0; /* Remove margin */
    }
    .modal-content {
        height: 100%; /* Full height */
        border-radius: 0; /* Remove border radius for seamless look */
    }
    .modal-body {
        display: flex;
        align-items: center;
        justify-content: center;
    }}
    /* Center posts on large screens with 50% width */
/* Center posts on large screens with 50% width */
@media (min-width: 851px) {
    .posts-section {
        width: 50%;  /* Set width to 50% */
        margin: 0 auto;  /* Center it */
    }

    /* Ensure post cards have appropriate height */
    .post-card {
        height: 100%; /* Allow posts to expand with content */
    }
}

/* Ensure post cards take full width on smaller screens */
@media (max-width: 850px) {
    .posts-section {
        width: 85%;
        margin: 0 auto;
    }
    .post-card {
        height: 100%; /* Ensure flexibility */
    }
}

/* Ensure posts maintain a certain minimum height on smaller screens */
@media (max-width: 576px) {
    .post-card {
        height: 100%; /* Ensure flexibility */
    }
}
.post-card img, .post-card video {
    height: 20rem;
}
.see-more-btn {
            color: #007bff;
            cursor: pointer;
        }
        .full-content {
            display: none;
        }

    </style>
</head>
<body>
<script>
    window.onload = function() {
    // Check if the session variable is set to show the success message
    <?php if (isset($_SESSION['post_success']) && $_SESSION['post_success'] === true): ?>
        // Show the success toast for 1.5 seconds
        var toast = document.createElement('div');
        toast.classList.add('toast');
        toast.textContent = 'Post was successfully created!';
        document.body.appendChild(toast);

        setTimeout(function() {
            toast.style.opacity = 0; // Fade out
            setTimeout(function() {
                toast.remove(); // Remove from DOM
            }, 500);
        }, 1500);

        // Unset the session variable to avoid showing the toast again on page reload
        <?php unset($_SESSION['post_success']); ?>
    <?php endif; ?>
};
function toggleContent(postId) {
    var fullContent = document.getElementById('full-content-' + postId);
    var seeMoreBtn = document.getElementById('see-more-btn-' + postId);
    var seeLessBtn = document.getElementById('see-less-btn-' + postId);

    if (fullContent.style.display === 'none') {
        fullContent.style.display = 'block';
        seeMoreBtn.style.display = 'none'; // Hide "See More"
        seeLessBtn.style.display = 'inline'; // Show "See Less"
    } else {
        fullContent.style.display = 'none';
        seeMoreBtn.style.display = 'inline'; // Show "See More"
        seeLessBtn.style.display = 'none'; // Hide "See Less"
    }
}

</script>

    <?php include 'nav.php'; ?>

    <!-- Profile Header -->
    <div class="profile-header">
        <?php if (!empty($user_info['profile_picture'])): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($user_info['profile_picture']); ?>" alt="Profile Picture" 
                 data-bs-toggle="modal" data-bs-target="#profilePictureModal">
        <?php else: ?>
            <img src="https://via.placeholder.com/120" alt="Profile Picture" 
                 data-bs-toggle="modal" data-bs-target="#profilePictureModal">
        <?php endif; ?>
        <h1><?php echo $name; ?></h1>
        <p><?php echo $email; ?></p>
        <div class="text-center mt-3">
            <a href="update_profile.php" class="btn btn-warning">Edit Profile</a>
        </div>
    </div>

    <!-- Modal for Enlarged Profile Picture -->
    <div class="modal fade" id="profilePictureModal" tabindex="-1" aria-labelledby="profilePictureModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profilePictureModalLabel">Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Display the enlarged profile picture here -->
                    <?php if (!empty($user_info['profile_picture'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($user_info['profile_picture']); ?>" alt="Enlarged Profile Picture">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/120" alt="Enlarged Profile Picture">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Image and Video -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaModalLabel">Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalMediaContent">
                <!-- Dynamic media content will go here -->
            </div>
        </div>
    </div>
</div>


    <!-- Post Form -->
    <div class="container mt-4">
        <h3>Post Your Thoughts</h3>
<form method="POST" action="" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="content" class="form-label">Content</label>
        <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
    </div>
    <div class="mb-3">
        <label for="media" class="form-label">Upload Media (Image or Video)</label>
        <input type="file" class="form-control" id="media" name="media" accept="image/*,video/*">
    </div>
    <button type="submit" class="btn btn-primary" name="submit_post">Post</button>
</form>

    </div>

    <!-- Posts Section -->
  <!-- Posts Section -->
  <div class="container posts-section">
        <h3 style="text-align:center;">Your Posts</h3>
        <?php if (!empty($post_result)): ?>
            <?php foreach ($post_result as $post): ?>
                <div class="post-card mb-4">
    <small>Posted on: <?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></small>
    <p>
        <?php
            $content = htmlspecialchars($post['content']);
            if (strlen($content) > 300) {
                // Display only first 300 characters
                echo substr($content, 0, 300) . '...';
                // Add the "See More" link
                echo '<span id="see-more-btn-' . $post['post_id'] . '" class="see-more-btn" onclick="toggleContent(' . $post['post_id'] . ')"> See More</span>';
                // Full content hidden by default
                echo '<span id="full-content-' . $post['post_id'] . '" class="full-content" style="display: none;">' . substr($content, 300) . '</span>';
                // Add "See Less" button at the end of the full content
                echo '<span id="see-less-btn-' . $post['post_id'] . '" class="see-more-btn" style="display: none;" onclick="toggleContent(' . $post['post_id'] . ')"> See Less</span>';
            } else {
                // Display full content if less than 300 characters
                echo $content;
            }
        ?>
    </p>
    <?php if (!empty($post['media']) && $post['media_type'] == 'image'): ?>
        <img src="<?php echo htmlspecialchars($post['media']); ?>" alt="Post Image">
    <?php elseif (!empty($post['media']) && $post['media_type'] == 'video'): ?>
        <video controls>
            <source src="<?php echo htmlspecialchars($post['media']); ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    <?php endif; ?>
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                <button type="submit" name="delete_post" class="btn btn-danger btn-sm mt-2">Delete</button>
    </form>
</div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No posts available.</p>
        <?php endif; ?>
    <!-- Delete post button -->
    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const mediaModal = new bootstrap.Modal(document.getElementById("mediaModal"));
    const modalContent = document.getElementById("modalMediaContent");

    // Add click listeners to images and videos
    document.querySelectorAll(".post-card img, .post-card video").forEach(media => {
        media.addEventListener("click", function () {
            let mediaSrc = null;

            if (media.tagName.toLowerCase() === "video") {
                // Get the source from the <source> tag inside the video
                const sourceElement = media.querySelector("source");
                if (sourceElement) {
                    mediaSrc = sourceElement.getAttribute("src");
                }
            } else if (media.tagName.toLowerCase() === "img") {
                // For images, get the src directly
                mediaSrc = media.getAttribute("src");
            }

            if (!mediaSrc) {
                console.error("Media source not found!");
                return;
            }

            // Clear the modal content
            modalContent.innerHTML = "";

            if (media.tagName.toLowerCase() === "img") {
                const imgElement = document.createElement("img");
                imgElement.src = mediaSrc;
                imgElement.alt = "Full-size Image";
                imgElement.classList.add("img-fluid");
                modalContent.appendChild(imgElement);
            } else if (media.tagName.toLowerCase() === "video") {
                const videoElement = document.createElement("video");
                videoElement.controls = true;
                videoElement.autoplay = true;
                videoElement.classList.add("img-fluid");

                const sourceElement = document.createElement("source");
                sourceElement.src = mediaSrc;
                sourceElement.type = "video/mp4";
                videoElement.appendChild(sourceElement);

                modalContent.appendChild(videoElement);
                videoElement.load();
            }

            mediaModal.show();
        });
    });

    // Clear modal content when hidden
    document.getElementById("mediaModal").addEventListener("hidden.bs.modal", function () {
        modalContent.innerHTML = "";
    });
});
window.onload = function() {
        <?php if (isset($_SESSION['delete_success']) && $_SESSION['delete_success'] === true): ?>
            var toast = document.createElement('div');
            toast.classList.add('toast');
            toast.textContent = 'Post was successfully deleted!';
            document.body.appendChild(toast);

            setTimeout(function() {
                toast.style.opacity = 0;
                setTimeout(function() {
                    toast.remove();
                }, 500);
            }, 1500);

            <?php unset($_SESSION['delete_success']); ?>
        <?php endif; ?>
    };





</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>