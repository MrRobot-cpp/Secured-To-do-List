<?php
require_once '../Model/Database.php';
require_once '../Model/User.php';

$database = new Database();
$conn = $database->getConnection();

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function login() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            $user = $this->userModel->verifyUser($email, $password);

            if ($user) {
                $this->startSession($user);
                echo "Login successful!"; 
            } else {
                echo "Invalid email or password";
            }
        }
    }

    public function signup() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
            $fullName = htmlspecialchars($_POST["name"]);
            $email = htmlspecialchars($_POST["email"]);
            $password = htmlspecialchars($_POST["password"]);
            $confirmPassword = htmlspecialchars($_POST["confirm_password"]);
            if (strlen($password) < 4) {
                $_SESSION['message'] = "Password must be at least 4 characters long.";
                return;
            }
            if (!$this->userModel->usernameExists($fullName) && !$this->userModel->emailExists($email)) {
                if ($password === $confirmPassword) {
                    if ($this->userModel->registerUser($fullName, $email, $password)) {
                        $_SESSION['message'] = "Registration successful!";
                    } else {
                        $_SESSION['message'] = "Error: Unable to register user.";
                    }
                } else {
                    $_SESSION['message'] = "Passwords do not match.";
                }
            } else {
                if ($this->userModel->usernameExists($fullName)) {
                    $_SESSION['message'] = "Username already exists.";
                }
                if ($this->userModel->emailExists($email)) {
                    $_SESSION['message'] = "Email already exists.";
                }
            }
        }
    }
    

    private function startSession($user) {
        $_SESSION["id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["email"] = $user["email"];
        header("Location: ../view/kanban.php?Login=success"); 
        exit();
    }
}

if ($conn) {
    $controller = new UserController($conn);

    if (isset($_POST['login'])) {
        $controller->login();
    } elseif (isset($_POST['signup'])) {
        $controller->signup();
    }
} else {
    echo "Database connection failed!";
}
?>