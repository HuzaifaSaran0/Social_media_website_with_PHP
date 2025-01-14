<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

$logged_in_user_id = $_SESSION['user_id'];
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

// Query to fetch posts with user like/dislike status and total likes count
$sql = "
    SELECT 
        posts.post_id, 
        posts.content, 
        posts.created_at, 
        users.username,
        posts.media,  -- Add media column
        posts.media_type,  -- Add media type (image, video)
        COALESCE(likes_data.likes_count, 0) AS likes_count,
        COALESCE(dislikes_data.dislikes_count, 0) AS dislikes_count,
        CASE 
            WHEN user_likes.is_liked = 1 THEN 'like'
            WHEN user_likes.is_liked = 0 THEN 'dislike'
            ELSE NULL
        END AS user_reaction
    FROM posts
    JOIN users ON posts.user_id = users.user_id
    LEFT JOIN (
        SELECT post_id, COUNT(*) AS likes_count
        FROM likes
        WHERE is_liked = 1
        GROUP BY post_id
    ) AS likes_data ON posts.post_id = likes_data.post_id
    LEFT JOIN (
        SELECT post_id, COUNT(*) AS dislikes_count
        FROM likes
        WHERE is_liked = 0
        GROUP BY post_id
    ) AS dislikes_data ON posts.post_id = dislikes_data.post_id
    LEFT JOIN likes AS user_likes ON posts.post_id = user_likes.post_id AND user_likes.user_id = ?
    WHERE posts.user_id != ? -- Exclude logged-in user's own posts
    ORDER BY posts.created_at DESC
    LIMIT ?, ?
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $logged_in_user_id, $logged_in_user_id, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

echo json_encode($posts);
$conn->close();

?>
