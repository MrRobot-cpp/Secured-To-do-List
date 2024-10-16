<?php
// Start session management for user authentication.
session_start();

// Load necessary dependencies.
require_once 'Model/Database.php';
require_once 'Controller/UserController.php';
require_once 'Controller/TaskController.php';

// Create a database connection.
$db = (new Database())->getConnection();
$userController = new UserController($db);
$taskController = new TaskController($db);

// Check if the user is logged in using session data.
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect them to the login page.
    header("Location: View/login.php");
    exit();
}

// If the user is logged in, direct them to the Kanban board.
header("Location: View/kanban.php");
exit();