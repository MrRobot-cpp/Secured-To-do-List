<?php
session_start();
include_once '../Controller/UserController.php';
include_once '../Model/Database.php';

$database = Database::getInstance();
$pdo = $database->getConnection();
$controller = new UserController($pdo);

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
            var confirmPassword = document.getElementById('confirmPassword').value;
            var passwordErrorMessage = document.getElementById('passwordErrorMessage');
            var confirmPasswordErrorMessage = document.getElementById('confirmPasswordErrorMessage');
            

            if (password.length < 4) {
                passwordErrorMessage.textContent = 'Password must be at least 4 characters long';
                passwordErrorMessage.style.display = 'block';
                return false;
            } else {
                passwordErrorMessage.style.display = 'none';
            }


            if (password !== confirmPassword) {
                confirmPasswordErrorMessage.textContent = 'Passwords do not match';
                confirmPasswordErrorMessage.style.display = 'block';
                return false;
            } else {
                confirmPasswordErrorMessage.style.display = 'none';
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

                    if (isset($_SESSION['otp_valid'])) {
                        echo '
                        <div class="fields">
                            <input type="password" name="password" id="password" placeholder="Enter new password" required>
                        </div>
                        <div id="passwordErrorMessage" class="error-message" style="display:none;"></div>
                        
                        <div class="fields">
                            <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm new password" required>
                        </div>
                        <div id="confirmPasswordErrorMessage" class="error-message" style="display:none;"></div>';
                    } else {
                        echo '<div class="error-message">Session expired or invalid OTP. Please try again.</div>';
                    }
                    ?>
                </div>
                <div class="fields button">
                    <input type="submit" value="Submit">
                </div>
            </form>
        </div>
    </div>
</body>
</html>