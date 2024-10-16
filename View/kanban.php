<?php
session_start();
require_once '../Model/Database.php';
require_once '../Controller/TaskController.php';

// Create database connection
$db = (new Database())->getConnection();
$taskController = new TaskController($db);

// Fetch tasks for the logged-in user
$userId = $_SESSION['user_id']; // Assuming user ID is stored in session
$tasks = $taskController->showTasks($userId);
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
    <div class="kanban-board">
        <div class="kanban-column" data-status="to_do">
            <h2>To Do</h2>
            <?php foreach ($tasks as $task): ?>
                <?php if ($task['status'] === 'to_do'): ?>
                    <div class="kanban-card">
                        <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                        <p><?php echo htmlspecialchars($task['description']); ?></p>
                        <p>Category: <?php echo htmlspecialchars($task['category_name']); ?></p>
                        <p>Priority: <?php echo htmlspecialchars($task['priority']); ?></p>
                        <p>Deadline: <?php echo htmlspecialchars($task['deadline']); ?></p>
                        <button class="move-to" data-task-id="<?php echo $task['id']; ?>" data-status="in_progress">Move to In Progress</button>
                        <button class="delete-task" data-task-id="<?php echo $task['id']; ?>">Delete</button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="kanban-column" data-status="in_progress">
            <h2>In Progress</h2>
            <?php foreach ($tasks as $task): ?>
                <?php if ($task['status'] === 'in_progress'): ?>
                    <div class="kanban-card">
                        <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                        <p><?php echo htmlspecialchars($task['description']); ?></p>
                        <!-- Similar details as above -->
                        <button class="move-to" data-task-id="<?php echo $task['id']; ?>" data-status="done">Move to Done</button>
                        <button class="delete-task" data-task-id="<?php echo $task['id']; ?>">Delete</button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="kanban-column" data-status="done">
            <h2>Done</h2>
            <?php foreach ($tasks as $task): ?>
                <?php if ($task['status'] === 'done'): ?>
                    <div class="kanban-card">
                        <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                        <p><?php echo htmlspecialchars($task['description']); ?></p>
                        <!-- Similar details as above -->
                        <button class="delete-task" data-task-id="<?php echo $task['id']; ?>">Delete</button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <button class="add-task-button">Add New Task</button>

    <script src="assets/js/kanban.js"></script>
</body>
</html>
