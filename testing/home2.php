<?php
//Start session at the top before any output
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit(); // Always exit after a redirect
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the post submission logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post'])) {
    $content = trim($_POST['content']);
    $media_type = null;
    $media_path = null;

    // Media upload handling
    if (!empty($_FILES['media']['name'])) {
        $file = $_FILES['media'];
        $target_dir = "uploads/";
        $file_name = basename($file['name']);
        $target_file = $target_dir . uniqid() . "_" . $file_name;

        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $media_type = 'image';
        } elseif (in_array($file_type, ['mp4', 'avi', 'mov', 'wmv'])) {
            $media_type = 'video';
        }

        if (!move_uploaded_file($file['tmp_name'], $target_file)) {
            echo "<script>alert('Failed to upload media file.');</script>";
        } else {
            $media_path = $target_file;
        }
    }

    if (!empty($content)) {
        $email = $_SESSION['email'];
        $query = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];

        $created_at = date('Y-m-d H:i:s');
        $sql_insert_post = $conn->prepare("INSERT INTO posts (user_id, content, media, media_type, created_at) VALUES (?, ?, ?, ?, ?)");
        $sql_insert_post->bind_param("issss", $user_id, $content, $media_path, $media_type, $created_at);

        if ($sql_insert_post->execute()) {
            $_SESSION['post_success'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Database error: " . $conn->error;
        }
    } else {
        echo "<script>alert('Please fill in content.');</script>";
    }
}
?>

<?php
$query = $conn->prepare("SELECT profile_picture FROM users WHERE email = ?");
$query->bind_param("s", $_SESSION['email']);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$profile_image = $user['profile_picture'] ?? 'default_image.jpg';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ali - Home</title>
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
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color:rgb(228, 226, 225);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* i want that navbar should be seen even when user sroll down */
        }

        /* Profile Banner */
        .profile-banner {
            background: linear-gradient(135deg, #ff6e40, #ff9100);
            color: white;
            padding: 3rem 1rem;
            text-align: center;
        }

        .profile-banner h1 {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .profile-banner p {
            font-size: 1.2rem;
            margin-top: 1rem;
        }

        /* Feed Section */
        .feed {
            padding: 3rem 1rem;
        }

        .post-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            padding: 1rem;
            height: auto;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .post-card img, .post-card video {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
        }

        .post-card h5 {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .post-card p {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3; /* Initially show 3 lines */
            -webkit-box-orient: vertical;
            transition: all 0.3s ease-in-out; /* Smoothly adjust height */
        }
        .post-card p {
            display: inline-block;
            position: relative;
        }

        .post-card p.expanded {
            -webkit-line-clamp: unset; /* Remove line clamping */
            overflow: visible; /* Allow full text to show */
            height: auto; /* Automatically adjust height */
        }

        .post-card footer {
            margin-top: 1rem;
        }

        footer {
            background-color:rgb(121, 125, 128);
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 3rem;
        }

        .no-more-posts {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 1.2rem;
        }

        .like-btn, .dislike-btn {
            border: none;
            background: none;
            cursor: pointer;
        }
        .comment-btn{
            border: none;
            background: none;
            cursor: pointer;
            padding: 0.6rem;
            margin-left: 5px;
            font-size: 0.9rem;
            text-decoration: none;
            color:rgb(0, 0, 0);
        }
        .like-btn.liked {
            color: blue;
        }

        .dislike-btn.disliked {
            color: red;
        }
        .see-more-btn, .see-less-btn {
            border: none;
            background: none;
            color: #007bff;
            cursor: pointer;
            padding: 0;
            margin-left: 5px;
            font-size: 0.9rem;
            text-decoration: underline;
        }
        #modalBodyContent img, #modalBodyContent video {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .feed {
            padding: 3rem 1rem;
        }

        .post-form {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            padding: 1rem;
        }

        .post-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            padding: 1rem;
        }

        footer {
            background-color: rgb(121, 125, 128);
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 3rem;
        }

        .no-more-posts {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 1.2rem;
        }

    </style>
</head>

<body>

    <!-- Navbar -->
    <?php include 'nav.php'; ?>

    <!-- Profile Banner -->
    <div class="profile-banner">
        <h1>Welcome to Ali!</h1>
        <p>Hello, <?php echo htmlspecialchars($_SESSION['email']); ?>! We're glad to have you here.</p>
        <!-- i want to show user image here too -->
    </div>
    <div class="container feed">
        <div class="row">
            <div class="col-md-6 offset-md-3">

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
                <br>

                <!-- Posts Container -->
                <div id="posts-container">
                    <!-- Posts will be dynamically loaded here -->
                </div>
            </div>
        </div>
        <div id="no-more-posts" class="no-more-posts" style="display: none;">
            <p>No more posts to show. <a href="profile_page.php">Create a new post</a> to keep the feed going!</p>
        </div>
    </div>

<!-- Main Feed Section -- -->
        <div id="no-more-posts" class="no-more-posts" style="display:none;">
            <p> 
                No more posts to show. <br>
            </p>

    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Ali. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    
    <script>
        let offset = 0;
        const limit = 5; // Number of posts to load at once
        let isLoading = false; // Flag to prevent duplicate requests
        const posts = []; // Array to hold posts data for easy access

        function loadPosts() {
            if (isLoading) return;
            isLoading = true;

            $.ajax({
                url: 'load_posts.php',
                method: 'GET',
                data: { offset: offset, limit: limit },
                    success: function(response) {
                        const loadedPosts = JSON.parse(response);
                        if (loadedPosts.length > 0) {
                            loadedPosts.forEach(post => {
                                posts.push(post); // Store the post for later reference

                                if ($(`#post-${post.post_id}`).length === 0) { // Prevent duplicate posts
                                    let mediaContent = '';
                                    let contentToDisplay = post.content;
                                    let showSeeMore = false;
                                    //  
                                    // Truncate content if it exceeds a certain length
                                    const maxContentLength = 150;
                                    if (contentToDisplay.length > maxContentLength) {
                                        contentToDisplay = contentToDisplay.substring(0, maxContentLength) + '...';
                                        showSeeMore = true;
                                    }

                                    // Check if media exists and its type
                                    if (post.media) {
                                        if (post.media_type === 'image') {
                                            mediaContent = `<img src="${post.media}" alt="Post Image" class="img-fluid">`;
                                        } else if (post.media_type === 'video') {
                                            mediaContent = ` 
                                                <video controls class="img-fluid">
                                                    <source src="${post.media}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            `;
                                        }
                                    }

                                    $('#posts-container').append(`
        <div class="post-card" id="post-${post.post_id}">
            <div class="post-header">
                            <hr>
                
                <a href="${post.username}"><span><b>@${post.username}</b></span></a>


                <span><i>(${post.created_at})</i></span>
                <hr>
            </div>
            <p id="post-content-${post.post_id}">
                ${contentToDisplay}
                ${showSeeMore ? `
                    <span>
                        <button class="see-more-btn" data-post-id="${post.post_id}">See More</button>
                        <button class="see-less-btn" data-post-id="${post.post_id}" style="display:none;">See Less</button>
                    </span>
                ` : ''}
            </p>
            ${mediaContent}
            <footer>
                <div class="mt-2">
                    <button class="btn btn-link comment-btn">
                    Comments
                    </button>

                    <button class="like-btn ${post.user_reaction === 'like' ? 'liked' : ''}" data-post-id="${post.post_id}">
                        👍 Like
                    </button>
                    <button class="dislike-btn ${post.user_reaction === 'dislike' ? 'disliked' : ''}" data-post-id="${post.post_id}">
                        👎 Dislike
                    </button>
                    <span id="likes-count-${post.post_id}">${post.likes_count} Likes</span>
                    <span id="dislikes-count-${post.post_id}">${post.dislikes_count} Dislikes</span>
                    <div class="comments-section">
      <div class="comments-list" id="comments-list-${post.post_id}">
    <!-- Comments will load dynamically here -->
</div>
<form class="comment-form" data-post-id="${post.post_id}">
    <textarea name="comment_text" placeholder="Write a comment..." class="form-control mb-2" required></textarea>
    <button type="submit" class="btn btn-primary btn-sm">Send</button>
</form>

    </div>

                </div>
            </footer>
        </div>
    `);
}
});
    offset += loadedPosts.length;
    } else {
        $('#no-more-posts').show();
        $(window).off('scroll');
    }
    isLoading = false;
},
error: function() {
    alert('Failed to load posts. Please try again.');
    isLoading = false;
                    }
                });
            }

        // Toggle "See More" / "See Less" functionality
// Toggle "See More" / "See Less" functionality
$(document).on('click', '.see-more-btn', function () {
    const postId = $(this).data('post-id');
    const fullContent = posts.find(post => post.post_id === postId).content;

    // Update content and toggle visibility of buttons
    $(`#post-content-${postId}`).html(`
        ${fullContent}
        <span>
            <button class="see-less-btn" data-post-id="${postId}">See Less</button>
        </span>
    `);
});

$(document).on('click', '.see-less-btn', function () {
    const postId = $(this).data('post-id');
    const truncatedContent = posts.find(post => post.post_id === postId).content.substring(0, 150) + '...';

    // Update content and toggle visibility of buttons
    $(`#post-content-${postId}`).html(`
        ${truncatedContent}
        <span>
            <button class="see-more-btn" data-post-id="${postId}">See More</button>
        </span>
    `);
});



        $(document).on('click', '.like-btn, .dislike-btn', function () {
            const postId = $(this).data('post-id');
            const action = $(this).hasClass('like-btn') ? 'like' : 'dislike';

            fetch('like_dislike_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ post_id: postId, action })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update likes and dislikes count
                        $(`#likes-count-${postId}`).text(`${data.likes} Likes`);
                        $(`#dislikes-count-${postId}`).text(`${data.dislikes} Dislikes`);

                        // Toggle button states
                        const likeBtn = $(`.like-btn[data-post-id="${postId}"]`);
                        const dislikeBtn = $(`.dislike-btn[data-post-id="${postId}"]`);

                        if (action === 'like') {
                            likeBtn.toggleClass('liked');
                            dislikeBtn.removeClass('disliked');
                        } else {
                            dislikeBtn.toggleClass('disliked');
                            likeBtn.removeClass('liked');
                        }
                    } else {
                        alert(data.message || 'An error occurred.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        $(document).ready(function() {
            loadPosts();
            $(window).on('scroll', function() {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                    loadPosts();
                }
            });
        });
        // Open modal on media click
$(document).on('click', '.post-card img, .post-card video', function () {
    const mediaElement = $(this).clone(); // Clone the clicked media element
    $('#modalBodyContent').html(mediaElement); // Inject the cloned element into the modal body
    $('#mediaModal').modal('show'); // Show the modal
});
// Load comments for a post
function loadComments(postId) {
    $.ajax({
        url: 'load_comments.php',
        method: 'GET',
        dataType: 'json',
        data: { post_id: postId },
        success: function(response) {
            // console.log('Raw response from server:', response); // Log raw response from PHP

            try {
                const comments = response;
                // console.log('Parsed comments:', comments);

                const commentsList = $(`#comments-list-${postId}`);
                commentsList.empty();

                if (comments.length > 0) {
            comments.forEach(comment => {
                commentsList.append(`
                    <div class="comment" style="border: 1px solid #ddd; border-radius: 8px; padding: 10px; margin-bottom: 10px; background-color: #f9f9f9;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <strong style="color: #333; font-size: 14px;">${comment.username}</strong>
                            <small style="color: #777; font-size: 12px;">${new Date(comment.comment_created_at).toLocaleString()}</small>
                        </div>
                        <p style="color: #555; font-size: 14px; margin: 0;">${comment.comment_text}</p>
                    </div>
                `);
            });
        } else {
            commentsList.append(`
                <p style="color: #777; text-align: center; font-size: 14px; margin-top: 10px;">No comments found.</p>
            `);
        }
            } catch (error) {
                console.error('Failed to parse comments:', error);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            console.log('Response text from server:', xhr.responseText); // Log raw error response
        }
    });
}


$(document).on('click', '.comment-btn', function (e) {

    const postId = $(this).closest('.post-card').attr('id').replace('post-', '');
    console.log('Post ID:', postId);
    loadComments(postId);
    
});

$(document).on('submit', '.comment-form', function (e) {
    e.preventDefault();

    const form = $(this);
    const postId = form.data('post-id');
    // console.log('Post ID:', postId);
    const commentText = form.find('textarea[name="comment_text"]').val();

    if (!commentText) {
        alert('Comment cannot be empty!');
        return;
    }

    $.ajax({
        url: 'submit_comment.php',
        method: 'POST',
        data: { post_id: postId, comment_text: commentText },
        success: function (response) {
            let result;
            try {
                result = JSON.parse(response);
            } catch (error) {
                console.error('Error parsing JSON:', error);
                alert('Failed to submit comment.');
                return;
            }

            if (result.success) {
                loadComments(postId);
                form[0].reset();
            } else {
                alert(result.message);
            }
        },
        error: function () {
            alert('Failed to submit comment.');
        },
    });
});

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



    </script>
    
    <!-- Modal -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaModalLabel">Media Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="modalBodyContent">
                <!-- Media content will be dynamically injected here -->
            </div>
        </div>
    </div>
</div>
<!-- Comments Modal -->
<div class="modal fade" id="commentsModal" tabindex="-1" aria-labelledby="commentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="commentsModalLabel">Comments</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Comments List -->
        <div id="modal-comments-list" class="list-group">
          <!-- Comments will be dynamically loaded here -->
        </div>
        <hr>
        <!-- Add Comment Form -->
        <form id="add-comment-form">
          <textarea name="comment_text" class="form-control mb-2" data-post-id="${post.post_id} placeholder="Write a comment..." required></textarea>
          <button type="submit" class="btn btn-primary">Add Comment</button>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
