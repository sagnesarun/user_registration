<?php
session_start();
include '../models/Like.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

if (isset($_POST['post_id'])) {
    $like = new Like();
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    error_log("Post ID: $post_id, User ID: $user_id");

    $like->toggleLike($post_id, $user_id);

    $like_count = $like->countLikes($post_id);

    error_log("Updated Like Count for Post ID $post_id: $like_count");

    echo json_encode([
        'like_count' => $like_count,
        'status' => 'success'
    ]);
} else {
    error_log("No post_id provided.");
    echo json_encode(['error' => 'No post_id provided']);
}
