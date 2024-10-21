<?php
include('../config/db.php');

if (isset($_POST['action']) && $_POST['action'] == 'fetch') {
    $post_id = $_POST['post_id'];
    
    $query = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = '$post_id' ORDER BY comments.created_at ASC";
    $result = mysqli_query($conn, $query);

    $comments = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }

    echo json_encode($comments);
}

if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];
    $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);

    $insert_query = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, 'iis', $post_id, $user_id, $comment_text);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(array('status' => 'success'));
    } else {
        echo json_encode(array('status' => 'error', 'error' => mysqli_error($conn)));
    }

    mysqli_stmt_close($stmt);
}
?>
