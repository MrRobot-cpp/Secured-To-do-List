<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function verifyUser($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email";
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

    public function usernameExists($fullName) {
        $sql = "SELECT * FROM users WHERE name = :fullname";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fullname', $fullName);
        $stmt->execute();

        return $stmt->rowCount() > 0; 
    }
    
    public function emailExists($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function registerUser($fullName, $email, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, Password) VALUES (:fullname, :email, :password)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fullname', $fullName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hash);

        return $stmt->execute(); 
    }


    public function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function get_id( $email) {
    try {
    
        $query = "SELECT id FROM USERS WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email,$password]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}

}
?>
