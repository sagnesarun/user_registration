<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'error' => 'You must be logged in to delete a comment.']);
        exit();
    }

    $comment_id = $_POST['comment_id'];

    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'error' => 'Error preparing statement: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param('ii', $comment_id, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'error' => 'Failed to delete comment or comment does not exist.']);
    }

    $stmt->close();
}
?>
