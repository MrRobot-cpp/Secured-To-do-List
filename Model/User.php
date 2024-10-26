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
// REGISTER
    public function registerUser($fullName, $email, $password, $usertypes_id) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, usertypes_id) VALUES (:name, :email, :password, :usertypes_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $fullName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':usertypes_id', $usertypes_id);
        
        return $stmt->execute();
    }

    public function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

// RESET PASS
    public function updatePassword($email, $newPassword) {
        $query = "UPDATE users SET password = :password WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $newPassword);
        $stmt->bindParam(':email', $email);
    
        return $stmt->execute();
    }

    public function get_id( $email) {
        try {

            $query = "SELECT id FROM users WHERE email = :email ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email',$email);
            $stmt->execute();

            return  $stmt->fetch(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
            return null;
        }
    }

    public function getAllUsers() {
        $query = "SELECT * FROM users WHERE usertypes_id = 2";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
