<?php
session_start();
include_once '../Controller/UserController.php';
include_once '../Model/Database.php';

$database = new Database();
$pdo = $database->getConnection();
$controller = new UserController($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['password'])) {
    $controller->updatePassword();  // Assuming you have a method to handle the password update
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../public/assets/css/reset_pass.css">
    <script>
        function validatePasswordForm() {
            var password = document.getElementById('password').value;
            var passwordErrorMessage = document.getElementById('passwordErrorMessage');
            
            if (password.length < 6) {
                passwordErrorMessage.textContent = 'Password must be at least 6 characters long';
                passwordErrorMessage.style.display = 'block';
                return false;
            } else {
                passwordErrorMessage.style.display = 'none';
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="form details">
            <span class="title">New Password</span>
            <form action="new_pass.php" method="POST" onsubmit="return validatePasswordForm()">
                <div>
                    <?php
                    if (isset($_SESSION['message'])) {
                        echo '<div class="success-message">' . $_SESSION['message'] . '</div>';
                        unset($_SESSION['message']);
                    }
                    if (isset($_SESSION['reset_message'])) {
                        echo '<div class="error-message">' . $_SESSION['reset_message'] . '</div>';
                        unset($_SESSION['reset_message']);
                    }

                    // Show new password field if OTP was validated
                    if (isset($_SESSION['otp_valid'])) {
                        echo '
                        <div class="fields">
                            <input type="password" name="password" id="password" placeholder="Enter new password" required>
                        </div>
                        <div id="passwordErrorMessage" class="error-message" style="display:none;"></div>';
                    } else {
                        echo '<div class="error-message">Session expired or invalid OTP. Please try again.</div>';
                    }
                    ?>
                </div>
                <div class="fields button">
                    <input type="submit" value="Submit ">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
