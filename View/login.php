<?php
session_start();
require_once '../Model/Database.php';
require_once '../Controller/UserController.php';

$database = Database::getInstance();
$conn = $database->getConnection();
$controller = new UserController($conn);
$login_message = isset($_SESSION['login_message']) ? $_SESSION['login_message'] : '';
$signup_message = isset($_SESSION['signup_message']) ? $_SESSION['signup_message'] : '';
unset($_SESSION['login_message']);
unset($_SESSION['signup_message']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="../public/assets/css/login.css">
    <title>Login</title>
</head>
<body>

   <div class="container">
    <div class="forms">
         <div class="form-details"> </div>
            <div class="form login">
            <span class="title">Login</span>

    <form id="loginpage" action="../Controller/UserController.php" method="POST">
        <div class="fields">
        <input id="loginemail" type="text" name="email" placeholder="Email" required>
        <i class="fa-regular fa-envelope icon"></i>
    </div>

    <div class="fields">
        <input id="loginpass" type="password" name="password" placeholder="password" required>
        <i class="fa-solid fa-lock icon"></i>
    </div>

    <div class="checkbox-field">


        <a href="reset_pass.php" class="text">Forgot password?</a>
    </div>

    <div class="fields button">
        <input type="submit" value="Login Now" name="login">
    </div>

    <div class="login-signup">
        <span class="text">Not a Member?
            <a href="#" class="text signup-link">Signup Now</a>
        </span>
    </div>
</form>
<?php if (!empty($login_message)) { echo '<div id="message" style="color:red;">'.$login_message.'</div>'; } ?>
   </div>
<!--  -->
   
<!-- Registration Form -->
   <div class="form signup" >
    <span class="title">Registration</span>

    <form id="signuppage" action="../Controller/UserController.php" method="POST">
        <div class="fields">
            <input id="name" type="text" name="name" placeholder="Fullname" required>
            <i class="fa-solid fa-signature icon"></i>
        </div>

        <div class="fields">
            <input id="email" type="email" name="email" placeholder="Email" required>
            <i class="fa-regular fa-envelope icon"></i>
        </div>

        <div class="fields">
            <input type="password" id="password" name="password" placeholder="password" required>
            <i class="fa-solid fa-lock icon"></i>
        </div>

        <div class="fields">
            <input type="password" id="confirm" name="confirm_password" placeholder="Confirm password" required>
            <i class="fa-solid fa-lock icon"></i>
        </div>

        <div class="fields button">
            <input type="submit" value="Signup" name="signup" required>
        </div>
    </form>
    <?php if (!empty($signup_message)) { echo '<div id="message" style="color:red;">'.$signup_message.'</div>'; } ?>
    
    <div class="login-signup">
        <span class="text">
            <a href="#" class="text login-link">Back to login</a>
        </span>
    </div>
   </div>

</div>
</div>

<script src="../public/js/login.js"></script>
<script>
    window.addEventListener('load', function() {
        if (performance.navigation.type === 1) {
            const messageDiv = document.getElementById('message');
            if (messageDiv) {
                messageDiv.style.display = 'none'; 
            }
        }
        const formElements = document.querySelectorAll('#signuppage input');
    formElements.forEach(element => {
        element.addEventListener('input', function() {
            const messageDiv = document.getElementById('message');
            if (messageDiv) {
                messageDiv.style.display = 'none'; 
            }
        });
    });
    const elements = document.querySelectorAll('#loginpage input');
    elements.forEach(element => {
        element.addEventListener('input', function() {
            const messageDiv = document.getElementById('message');
            if (messageDiv) {
                messageDiv.style.display = 'none'; 
            }
        });
    });
    });
    
    
</script>
</body>
</html>
