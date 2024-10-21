<?php
class User {
    private $conn;

    public function __construct() {
        include '../config/db.php';
        $this->conn = $conn;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>
