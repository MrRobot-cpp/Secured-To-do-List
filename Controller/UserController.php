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

    public function __construct($db) {
        $this->userModel = new User($db);
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
           
            public function SendEmail($email,$Subject,$body){
                $mail=new PHPMailer(true);
                $miamail="wandenreich111@gmail.com";
                $mianame="kanaban@nonreply";
                $miapassword="azehhtmxgxtevpgc";
                
                try {
                    $mail->SMTPDebug=SMTP::DEBUG_OFF;
                    $mail->isSMTP();
                    $mail->Host='smtp.gmail.com';
                    $mail->SMTPAuth=true;
                    $mail->Username=$miamail;
                    $mail->Password=$miapassword;
                    $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port=587;
                
                    $mail->setFrom($miamail,$mianame);
                    $mail->addAddress($email);
                    $mail->Subject="$Subject";
                    $mail->Body="$body";
                    $mail->send();
                    return true;
                
                
                } catch (Exception $e) {
                    echo"mailing error".$e->getMessage();
               echo "error in sending the mail".$mail->ErrorInfo;
                    
                    return false;
                }
              
            }
        public function VerificationEmail($email,$verificationCode){
            $body="your verification code is $verificationCode";
            $subject="Verification Email";
              $this->SendEmail($email,$subject,$body);  
                }

                
            public function checkDeadline($deadline) {
                    $subject=" a late task";
                    $body="your deadline has passed";
                                $deadlineDate = new DateTime($deadline);
                                $currentDate = new DateTime();
                            
                                $interval = $currentDate->diff($deadlineDate);
                            
                                if ($currentDate > $deadlineDate) {
                                    $this->SendEmail($_SESSION['email'],$subject,$body);
                                    return "The deadline has passed.";
                                } else {
                                    return "Time remaining: " . $interval->format('%d days, %h hours, %i minutes');
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
                'password' => password_hash($password, PASSWORD_DEFAULT),
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


    public function resetPassword() {
        session_start();
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
            $email = filter_var($_POST['reset_email'], FILTER_SANITIZE_EMAIL); 
            $newPassword = htmlspecialchars($_POST["new_password"]);
            $confirmPassword = htmlspecialchars($_POST["confirm_password"]);
            if ($this->userModel->emailExists($email)) {
                if ($newPassword === $confirmPassword) {
                    if ($this->userModel->updatePassword($email, password_hash($newPassword, PASSWORD_DEFAULT))) {
                        $_SESSION['fmessage'] = "Password reset successfully!";
                    } else {
                        $_SESSION['fmessage'] = "Error resetting password.";
                    }
                } else {
                    $_SESSION['fmessage'] = "Passwords do not match.";
                }
            } else {
                $_SESSION['fmessage'] = "Email does not exist.";
            }
        }
        header("Location: ../view/reset_pass.php");
        exit();
    }

    public function getUserById($userId) {
        return $this->userModel->getUserById($userId);
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
    } elseif (isset($_POST['reset_password'])) { 
        $controller->resetPassword();
    }
    elseif  (isset($_POST['verify'])){
        $controller->verify();
    }
} else {
    echo "Database connection failed!";
}
