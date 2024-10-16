<?php

require_once '../Model/Database.php';
require_once '../Model/User.php'; // Include the User model

// Initialize the database connection
$database = new Database();
$conn = $database->getConnection(); // Get the PDO connection

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db); // Pass the PDO connection to the User model
    }

    public function login() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            // Verify user using the User model
            $user = $this->userModel->verifyUser($email, $password);

            if ($user) {
                $this->startSession($user);
            } else {
                echo '<script>
                alert("Invalid email or password");
                window.location.href="login.php";
                </script>';
            }
        }
    }

    private function startSession($user) {
        $_SESSION["ID"] = $user["ID"];
        $_SESSION["name"] = $user["Fullname"];
        $_SESSION["email"] = $user["Email"];
        header("Location: welcome1.php?Login=success");
        exit();
    }
}

// Check if the connection is successful before passing it to UserController
if ($conn) {
    $controller = new UserController($conn);
    $controller->login();
} else {
    echo "Database connection failed!";
}
?>
