<?php
class Comment {
    private $conn;

    public function __construct() {
        include '../config/db.php';
        $this->conn = $conn;
    }

    public function getByPostId($postId) {
        $stmt = $this->conn->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at ASC");

        if (!$stmt) {
            die("Error preparing statement: " . $this->conn->error);
        }

        $stmt->bind_param('i', $postId);
        $stmt->execute();
        
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return [];
        }

        $comments = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $comments;
    }

    public function addComment($post_id, $user_id, $content) {
        $stmt = $this->conn->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");

        if (!$stmt) {
            die("Error preparing statement: " . $this->conn->error);
        }

        $stmt->bind_param('iis', $post_id, $user_id, $content);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            die("Failed to insert comment.");
        }

        $stmt->close();
    }
}
