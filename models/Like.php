<?php
class Like {
    private $conn;

    public function __construct() {
        include '../config/db.php';
        $this->conn = $conn;
    }

    public function countLikes($postId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?");
        $stmt->bind_param('i', $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['like_count'];
    }

    public function hasLiked($postId, $userId) {
        $stmt = $this->conn->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param('ii', $postId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function toggleLike($postId, $userId) {
        if ($this->hasLiked($postId, $userId)) {
            $this->removeLike($postId, $userId);
        } else {
            $this->addLike($postId, $userId);
        }
    }

    private function addLike($postId, $userId) {
        $stmt = $this->conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $postId, $userId);
        $stmt->execute();
    }

    private function removeLike($postId, $userId) {
        $stmt = $this->conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param('ii', $postId, $userId);
        $stmt->execute();
    }
}
