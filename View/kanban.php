<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/PriorityFactory.php';
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
                // Define columns based on statuses.
                $statuses = ['todo' => 'To do', 'inprogress' => 'In Progress', 'finished' => 'Finished'];

                foreach ($statuses as $status_key => $status_name) {
                    echo "<div class='kanban-column' data-status='$status_key'>";
                    echo "<h2>$status_name</h2>";

                    foreach ($tasks as $task) {
                        if ($task['status'] === $status_key) {
                            $priorityObject = PriorityFactory::createPriority($task['priority']);
                            $priorityDisplay = $priorityObject->getPriorityLevel();
                            echo "<div draggable=true class='task' data-title='".htmlspecialchars($task['title'])."' data-priority='" . htmlspecialchars($priorityDisplay)  . "' data-status='$status_key' data-deadline='" . htmlspecialchars($task['deadline']) . "'>";
                            echo "<h3>" . htmlspecialchars($task['title']) . "</h3>";
                            echo "<p>" . htmlspecialchars($task['description']) . "</p>";
                            echo "<button class='update-task' data-task-id='" . htmlspecialchars($task['id']) . "'>Update</button>"; 
                            echo "<button class='delete-task' data-task-id='" . htmlspecialchars($task['id']) . "'>delete</button>"; 
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
            <input type="date" name="deadline" required>

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

    <!-- Update Task Form -->
    <form id="update-form" action="../Controller/TaskController.php" method="POST" style="display: none;">
        <input type="text" name="title" placeholder="Task Title" required>
        <textarea name="description" placeholder="Task Description"></textarea>
        <input type="date" name="deadline" required>

        <select name="priority">
            <option value="urgent">Urgent</option>
            <option value="high">High</option>
            <option value="normal">Normal</option>
        </select>
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
        <input type="hidden" name="project_id" value="<?php echo $_GET['project_id']; ?>" />
        <input type="hidden" name="task_id" value="">
        <input type="hidden" name="action" value="update_task">

        <button type="submit" name="update_task">Update Task</button>
        <button type="button" id="update-form-cancel">Cancel</button>
    </form>

    <!-- Delete Task Form -->
    <form id="delete-form" action="../Controller/TaskController.php" method="POST" style="display: none;">
        <input type="hidden" name="task_id" value="">
        <input type="hidden" name="action" value="delete_task">
    </form>
    
        <!--theme toggle-->
    <div id="theme-toggle-container">
    <img id="theme-toggle-button" src="../public/assets/img/themeLogo.png" alt="Theme Toggle">
    <div class="theme-selector hidden" id="theme-dropdown-container">
        <ul>
            <li class="theme-option" data-theme="">Default</li>
            <li class="theme-option" data-theme="monochrome-theme">Blue</li>
            <li class="theme-option" data-theme="forest-theme">Green</li>
        </ul>
    </div>
    <!--end theme toggle-->
</div>




    <script src="../public/js/kanban.js"></script>
</body>
</html>