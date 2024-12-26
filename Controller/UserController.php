<?php
require_once '../Model/Database.php';
require_once '../Model/User.php';
require_once __DIR__ . '\PHPMailer-master\src\Exception.php';
require_once __DIR__ . '\PHPMailer-master\src\PHPMailer.php';
require_once __DIR__ . '\PHPMailer-master\src\SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$database = new Database();
$conn = $database->getConnection();

class UserController {
    private $userModel;
    private $errorMessage = '';

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function handleOtpRequest() {
        if (isset($_POST['email'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            if ($this->userModel->emailExists($email)) { 
                $verificationCode = $this->VerificationCode();
                $_SESSION['verification_code'] = $verificationCode;
    
                if ($this->VerificationEmail($email, $verificationCode)) {
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['message'] = "OTP sent successfully to your email.";
                    header("Location: ../view/enterOTP.php");
                    exit();
                } else {
                    $_SESSION['reset_message'] = "Failed to send OTP. Please try again.";
                }
            } else {
                $_SESSION['reset_message'] = "Email does not exist in our database.";
            }
    
            header("Location: ../view/reset_pass.php");
            exit();
        }
    }

    // Validate the OTP
    public function validateOtp() {
        if (isset($_POST['otp'])) {
            $email = $_SESSION['reset_email'] ?? null;
            $enteredOtp = htmlspecialchars($_POST['otp']);

            if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] === $enteredOtp) {
                $_SESSION['otp_valid'] = true;
                $_SESSION['message'] = "enter a new password";
                header("Location: ../view/new_pass.php");
                exit();
            } else {
                $_SESSION['reset_message'] = "Invalid OTP. Please try again.";
                header("Location: ../view/enterOTP.php"); 
                exit();
            }
        }
    }

    // Update the password
    public function updatePassword() {
        if (isset($_POST['password'])) {
            $email = $_SESSION['reset_email'] ?? null;
            $newPassword = htmlspecialchars($_POST['password']);
            $isUpdated = $this->userModel->updatePassword($email, $newPassword);

            if ($isUpdated) {
                $_SESSION['message'] = "Password updated successfully!";
                unset($_SESSION['otp_valid']); 
                header("Location: login.php"); 
                exit();
            } else {
                $_SESSION['reset_message'] = "Error updating password. Please try again.";
            }
        }
    }

    public function VerificationCode ($length=6){
        session_start();
        
            $characters='0123456789';
            $code="";
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters [rand(0, strlen($characters)-1)];
            }
            $_SESSION['verification']=$code;
            return $code;
            }
        public function VerificationEmail($email,$verificationCode){
                $mail=new PHPMailer(true);
                $miamail="wandenreich111@gmail.com";
                $mianame="kanaban@nonreply";
                $miapassword="azehhtmxgxtevpgc";
                
                try {
                    $mail->SMTPDebug=SMTP::DEBUG_OFF;//return it back t off after fininshing the debuging
                    $mail->isSMTP();
                    $mail->Host='smtp.gmail.com';
                    $mail->SMTPAuth=true;
                    $mail->Username=$miamail;
                    $mail->Password=$miapassword;
                    $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port=587;
                
                    $mail->setFrom($miamail,$mianame);
                    $mail->addAddress($email);
                    $mail->Subject='Email Verification';
                    $mail->Body="your verification code is: $verificationCode";
                    $mail->send();
                    return true;
                
                
                } catch (Exception $e) {
                    echo"mailing error".$e->getMessage();
               echo "error in sending the mail".$mail->ErrorInfo;
                    
                    return false;
                }
                
                }
    public function login() {
        session_start();
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $user = $this->userModel->verifyUser($email, $password);
            if ($user) {
                $this->startSession($user);
                if ($user['usertypes_id'] === 1) { 
                    header("Location: ../view/adminDashboard.php?Login=success"); 
                } else {
                    header("Location: ../view/kanban.php?Login=success"); 
                }
                exit();
            } else {
                $_SESSION['login_message'] = "Invalid Email or Password";
                header("Location: ../view/login.php"); 
                exit();
            }
        }
    }

    public function verify() {
        session_start();
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verification_code'])) {
            $clientCode = htmlspecialchars($_POST['verification_code']);
            $storedCode = $_SESSION['verification'] ?? null;
    
                   if ($clientCode === $storedCode && isset($_SESSION['temp_user'])) {
               
                $tempUser = $_SESSION['temp_user'];
    
                
                if ($this->userModel->registerUser($tempUser['name'], $tempUser['email'], $tempUser['password'], $tempUser['userType'])) {
                    unset($_SESSION['temp_user'], $_SESSION['verification']);
                    $_SESSION['message'] = "Account verified and created successfully!";
                    header("Location: ../view/login.php");
                    exit();
                } else {
                    $_SESSION['message'] = "Error creating account.";
                    header("Location: ../view/verification.php");
                    exit();
                }
            } else {
                $_SESSION['message'] = "Invalid verification code.";
                header("Location: ../view/verification.php");
                exit();
            }
        }
    }
    
    public function signup() {
        session_start();
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
            $fullName = htmlspecialchars($_POST["name"]);
            $email = htmlspecialchars($_POST["email"]);
            $password = htmlspecialchars($_POST["password"]);
            $confirmPassword = htmlspecialchars($_POST["confirm_password"]);
            $userType = 2;
    
            if (strlen($password) < 4) {
                $_SESSION['signup_message'] = "Password must be at least 4 characters long.";
                header("Location: ../view/signup.php");
                exit();
            }
    
            if ($password !== $confirmPassword) {
                $_SESSION['signup_message'] = "Passwords do not match.";
                header("Location: ../view/signup.php");
                exit();
            }
    
            if ($this->userModel->usernameExists($fullName) || $this->userModel->emailExists($email)) {
                $_SESSION['signup_message'] = "Username or email already exists.";
                header("Location: ../view/signup.php");
                exit();
            }
    
            $_SESSION['temp_user'] = [
                'name' => $fullName,
                'email' => $email,
                'password' => $password,
                'userType' => $userType
            ];
    
            $verificationCode = $this->VerificationCode();
            if ($this->VerificationEmail($email, $verificationCode)) {
                $_SESSION['signup_message'] = "Check your inbox for verification";
                header("Location: ../view/verification.php");
                exit();
            } else {
                $_SESSION['signup_message'] = "Error sending verification email.";
                header("Location: ../view/signup.php");
                exit();
            }
        }
    }
    
    
        public function get_id($email){
return $this->userModel->get_id($email);
    }
    public function getAllUsers() {
        return $this->userModel->getAllUsers(); // Call the new method
    }

    public function getUserById($userId) {
        return $this->userModel->getUserById($userId);
    }

    public function getErrorMessage() {
        return $this->errorMessage;
    }

    private function startSession($user) {
        session_start();
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["usertypes_id"] = $user["usertypes_id"];
    }
}


if ($conn) {
    $controller = new UserController($conn);

    if (isset($_POST['login'])) {
        $controller->login();
    } elseif (isset($_POST['signup'])) {
        $controller->signup();
    }  elseif  (isset($_POST['verify'])){
        $controller->verify();
    } elseif (isset($_POST['action']) && $_POST['action'] === 'reset_pass') {
        $controller->handleOtpRequest();
    } elseif (isset($_POST['otp'])) {
        $controller->validateOtp(); 
    } elseif (isset($_POST['password']) ) {
        $controller->updatePassword();  
    }
} else {
    echo "Database connection failed!";
}

?>