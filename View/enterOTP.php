<?php
session_start();
include_once '../Controller/UserController.php';
include_once '../Model/Database.php';

$database = Database::getInstance();
$pdo = $database->getConnection();
$controller = new UserController($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['otp'])) {
    $controller->validateOtp();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP</title>
    <link rel="stylesheet" href="../public/assets/css/reset_pass.css">
    <script>
        function validateForm() {
            var otp = document.getElementById('otp').value;
            var otpErrorMessage = document.getElementById('otpErrorMessage');
            
            if (otp.length === 0) {
                otpErrorMessage.textContent = 'OTP is required';
                otpErrorMessage.style.display = 'block';
                return false;
            } else {
                otpErrorMessage.style.display = 'none';
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="form details">
            <span class="title">Enter OTP</span>
            <form action="enterOTP.php" method="POST" onsubmit="return validateForm()">
                <div>
                    <?php
                    // Display any success or error messages
                    if (isset($_SESSION['message'])) {
                        echo '<div class="success-message">' . $_SESSION['message'] . '</div>';
                        unset($_SESSION['message']);
                    }
                    if (isset($_SESSION['reset_message'])) {
                        echo '<div class="error-message">' . $_SESSION['reset_message'] . '</div>';
                        unset($_SESSION['reset_message']);
                    }

                    // OTP input field
                    echo '
                    <div class="fields">
                        <input type="text" name="otp" id="otp" placeholder="Enter OTP" required>
                    </div>
                    <div id="otpErrorMessage" class="error-message" style="display:none;"></div>';
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
