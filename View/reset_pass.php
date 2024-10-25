<?php
session_start();
require_once '../Model/Database.php';
require_once '../Controller/UserController.php';

$database = new Database();
$conn = $database->getConnection();
$controller = new UserController($conn);
$fmessage = isset($_SESSION['fmessage']) ? $_SESSION['fmessage'] : '';
unset($_SESSION['fmessage']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="../public/assets/css/reset_pass.css">
    <title>Forgot Password</title>
</head>
<body>
    <div class="container">
        <div class="form forgot-password">
            <span class="title">Forgot Password</span>
            <form id="forgotPasswordForm" action="" method="POST">
                <div class="fields">
                    <input type="email" name="reset_email" placeholder="Email" required> 
                    <i class="fa-regular fa-envelope icon"></i>
                </div>

                <div class="fields">

                    <input type="password" name="new_password" placeholder="New Password" required>
                    <i class="fa-solid fa-lock icon"></i>
                </div>

                <div class="fields">

                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                    <i class="fa-solid fa-lock icon"></i>
                </div>

                <div class="fields button">
                    <input type="submit" value="Reset Password" name="reset_password">
                </div>
            </form>
            <?php if (!empty($fmessage)) { echo '<div id="fmessage" style="color:red;">'.$fmessage.'</div>'; } ?>
            <div class="login-signup">
        <span class="text">
            <a href="../view/login.php" class="text login-link" id="back">Back to login</a>
        </span>
    </div>
   </div>
        </div>
    </div>

    <script src="../public/js/login.js"></script>
    <script>
    window.addEventListener('load', function() {
        if (performance.navigation.type === 1) {
            const messageDiv = document.getElementById('fmessage');
            if (messageDiv) {
                messageDiv.style.display = 'none'; 
            }
        }
        const formElements = document.querySelectorAll('#forgotPasswordForm input');
    formElements.forEach(element => {
        element.addEventListener('input', function() {
            const messageDiv = document.getElementById('fmessage');
            if (messageDiv) {
                messageDiv.style.display = 'none'; 
            }
        });
    });

    });
    
    
</script>
</body>
</html>
