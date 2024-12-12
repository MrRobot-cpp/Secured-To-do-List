
<?php
// Start the session and include necessary files.
session_start();
require_once '../Model/Database.php';
require_once '../Controller/ProjectController.php';
require_once '../Controller/TaskController.php';
require_once '../Controller/UserController.php';



// Create a new database connection and controllers.
$db = (new Database())->getConnection();
$userController = new UserController($db);
$taskController = new TaskController($db);

// Retrieve user data.
$user = $userController->getUserById($_SESSION['user_id']);
$name = $user['name'] ?? 'User';
$usertypes_id = $user['usertypes_id'] ?? null;  // Fetch usertypes_id
// Get the project_id from the query string
$projectId = isset($_GET['project_id']) ? intval($_GET['project_id']) : null;



// Fetch tasks for the specific project
$tasks = $taskController->getTasksByProjectId($projectId);
if ($usertypes_id != 1&&!$projectId) {
    header("Location: projects.php");
    exit();
}
if ($usertypes_id != 2) {
    header("Location: adminDashboard.php");
    exit();
}
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

    <!-- Task Form -->
    <div id="task-form" style="display: none;">
        <form action="../Controller/TaskController.php" method="POST">
            <input type="text" name="title" placeholder="Task Title" required>
            <textarea name="description" placeholder="Task Description"></textarea>
            <select name="priority">
                <option value="urgent">Urgent</option>
                <option value="high">High</option>
                <option value="normal">Normal</option>
            </select>
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" name="project_id" value="<?php echo $_GET['project_id']; ?>" />

            <button type="submit" name="add_task">Add Task</button>
            <button type="button" id="task-form-cancel">Cancel</button>
        </form>
    </div>
            <!--theme toggle-->
    <div id="theme-toggle-container">
    <img id="theme-toggle-button" src="../public/assets/img/themeLogo.png" alt="Theme Toggle">
    <div class="theme-selector hidden" id="theme-dropdown-container">
        <ul>
            <li class="theme-option" data-theme="">Default</li>
            <li class="theme-option" data-theme="monochrome-theme">Monochrome</li>
            <li class="theme-option" data-theme="forest-theme">Forest</li>
        </ul>
    </div>
           <!--end theme toggle-->
</div>




    <script src="../public/js/kanban.js"></script>
</body>
</html>