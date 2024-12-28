<?php

session_start();

    include_once '../Controller/UserController.php';
    require_once '../Model/Database.php';
    // Initialize the database and controller
    $database = Database::getInstance();
    $conn = $database->getConnection();
    $controller = new UserController($conn);
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
            <form id="forgotPasswordForm" action="../Controller/UserController.php" method="POST">
            <input type="hidden" name="action" value="reset_pass">
                <div class="fields">
                    <input type="email" name="email" placeholder="Enter your email" required> 
                    <i class="fa-regular fa-envelope icon"></i>
                </div>
                <div class="fields button">
                    <input type="submit" value="Send OTP">
                </div>
            </form>

            <?php if (isset($_SESSION['fmessage'])) { ?>
                <div id="fmessage" style="color:red;">
                    <?php echo $_SESSION['fmessage']; ?>
                </div>
                <?php unset($_SESSION['fmessage']); // Clear the message ?>
            <?php } ?>

            <div class="login-signup">
                <span class="text">
                    <a href="login.php" class="text login-link" id="back">Back to login</a>
                </span>
            </div>
        </div>
    </div>

    <script>
        // Hide the flash message when the form changes
        document.querySelector('#forgotPasswordForm').addEventListener('input', () => {
            const messageDiv = document.getElementById('fmessage');
            if (messageDiv) {
                messageDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>
