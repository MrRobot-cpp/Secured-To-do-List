<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Verify the user by email and password
    public function verifyUser($email, $password) {
        $sql = "SELECT * FROM users WHERE Email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }
}
?>
