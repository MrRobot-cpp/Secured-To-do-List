<?php
class User {
    private $conn;
    private $encryptionKey = "your-secret-key"; 
    private $ciphering = "AES-128-CTR";
    private $options = 0;
    private $encryptionIv = '1234567891011121';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Encrypt the email
    private function encryptEmail($email) {
        return openssl_encrypt($email, $this->ciphering, $this->encryptionKey, $this->options, $this->encryptionIv);
    }

    // Decrypt the email
    private function decryptEmail($encryptedEmail) {
        return openssl_decrypt($encryptedEmail, $this->ciphering, $this->encryptionKey, $this->options, $this->encryptionIv);
    }

    // Get user by email
    public function getUserByEmail($email) {
        $encryptedEmail = $this->encryptEmail($email); 
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['email' => $encryptedEmail]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verify user by email and password
    public function verifyUser($email, $password) {
        $encryptedEmail = $this->encryptEmail($email); 
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $encryptedEmail);
        $stmt->execute();
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $user['password'])) { 
                return $user;
            }
        }
        return false;
    }

    // Check if username exists
    public function usernameExists($fullName) {
        $sql = "SELECT * FROM users WHERE name = :fullname";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fullname', $fullName);
        $stmt->execute();

        return $stmt->rowCount() > 0; 
    }
    
    // Check if email exists
    public function emailExists($email) {
        $encryptedEmail = $this->encryptEmail($email); 
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $encryptedEmail);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Register a new user
    public function registerUser($fullName, $email, $password, $usertypes_id) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 
        $encryptedEmail = $this->encryptEmail($email);
        $sql = "INSERT INTO users (name, email, password, usertypes_id) VALUES (:name, :email, :password, :usertypes_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $fullName);
        $stmt->bindParam(':email', $encryptedEmail);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':usertypes_id', $usertypes_id);

        return $stmt->execute();
    }

    // Get user by ID
    public function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update user password
    public function updatePassword($email, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $encryptedEmail = $this->encryptEmail($email); 
        $query = "UPDATE users SET password = :password WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $encryptedEmail);

        return $stmt->execute(); 
    }

    // Get user ID by email
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


    // Get all users
    public function getAllUsers() {
        $query = "SELECT * FROM users WHERE usertypes_id = 2";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as &$user) {
            $user['email'] = $this->decryptEmail($user['email']); 
        }
        return $users;
    }
}
?>