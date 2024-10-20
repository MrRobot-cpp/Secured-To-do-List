<?php
// Start the session and include necessary files.
session_start();
require_once '../Model/Database.php';
require_once '../Controller/TaskController.php';
require_once '../Controller/UserController.php';

// Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Create a new database connection and controllers.
$db = (new Database())->getConnection();
$userController = new UserController($db);
$taskController = new TaskController($db);

// Retrieve user data.
$user = $userController->getUserById($_SESSION['user_id']);
$name = $user['name'] ?? 'User';

// Retrieve all tasks for the user.
$tasks = $taskController->getAllTasksByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Board</title>
    <link rel="stylesheet" href="../public/assets/css/kanban.css">
</head>
<body>
    <div class="container">
        <!-- Vertical Navbar -->
        <aside class="navbar">
            <h2>Welcome, <?php echo htmlspecialchars($name); ?></h2>
            <ul>
                <li><a href="kanban.php" class="active">Home</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="kanban-board">
            <div class="search-filter">
                <input type="text" id="search-bar" placeholder="Search tasks...">
                <div class="filter-group">
                    <label for="priority-filter">Priority:</label>
                    <select id="priority-filter">
                        <option value="all">All</option>
                        <option value="urgent">Urgent</option>
                        <option value="high">High</option>
                        <option value="normal">Normal</option>
                    </select>
                </div>
            </div>

            <div class="kanban-columns">
                <?php 
                // Define columns based on priorities.
                $priorities = ['urgent' => 'Urgent', 'high' => 'High', 'normal' => 'Normal'];

                foreach ($priorities as $priority_key => $priority_name) {
                    echo "<div class='kanban-column' data-priority='$priority_key'>";
                    echo "<h2>$priority_name</h2>";

                    foreach ($tasks as $task) {
                        if ($task['priority'] === $priority_key) {
                            echo "<div class='task' data-title='".htmlspecialchars($task['title'])."' data-priority='$priority_key'>";
                            echo "<h3>" . htmlspecialchars($task['title']) . "</h3>";
                            echo "<p>" . htmlspecialchars($task['description']) . "</p>";
                            echo "</div>";
                        }
                    }
                    echo "<div class='new-task'>+ New Task</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </main>
    </div>

    <script src="../public/js/kanban.js"></script>
</body>
</html>
