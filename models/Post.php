<?php
class Post {
    private $conn;

    public function __construct() {
        include '../config/db.php';
        $this->conn = $conn;
    }

    public function create($content, $user_id) {
        $stmt = $this->conn->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
        $stmt->bind_param('is', $user_id, $content);
        $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT posts.*, users.username 
                  FROM posts 
                  JOIN users ON posts.user_id = users.id 
                  ORDER BY posts.created_at DESC";
        
        $result = $this->conn->query($query);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT posts.*, users.username 
                                       FROM posts 
                                       JOIN users ON posts.user_id = users.id 
                                       WHERE posts.user_id = ? 
                                       ORDER BY posts.created_at DESC");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>
