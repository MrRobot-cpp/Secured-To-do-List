<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../Model/Task.php';
require_once 'UserController.php';
class TaskController {
    private $taskModel;

    public function __construct($db) {
        $this->taskModel = new Task($db);
    }

    // Display tasks for the Kanban board
    public function showTasks($userId) {
        return $this->taskModel->getTasksByUserId($userId);
    }

    // Create a new task
    public function createTask($userId, $title, $description, $status, $categoryId, $priority,  $deadline) {
        return $this->taskModel->createTask($userId, $title, $description, $status,$categoryId, $priority,  $deadline);
    }

    // Update task status (for dragging between columns)
    public function updateTaskStatus($taskId, $status) {
        return $this->taskModel->updateTaskStatus($taskId, $status);
    }

    // Update a task (full update)
    public function updateTask($taskId, $title, $description, $categoryId, $priority, $status, $deadline) {
        return $this->taskModel->updateTask($taskId, $title, $description, $categoryId, $priority, $status, $deadline);
    }

    // Delete a task
    public function deleteTask($taskId) {
        return $this->taskModel->deleteTask($taskId);
    }

    public function getAllTasksByUser($userId) {
        return $this->taskModel->getTasksByUserId($userId);
    }
}

// Handle the POST request for task creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    require_once '../Model/Database.php';
    require_once '../Controller/UserController.php';
    
    $db = (new Database())->getConnection();
    $taskController = new TaskController($db);
    $user=new UserController($db);
    //echo $_SESSION["email"];
    
    $userId =$user->get_id($_SESSION["email"]);
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $priority = $_POST['priority'] ?? 'normal';
    $status = $_POST['status'] ?? 'normal'; // Status corresponds to the column it's in
    $categoryId = 1; // Set default or update as needed.
    $deadline = $_POST['deadline'] ?? null;

    if (!empty($title) || $userId > 0) {
        $result = $taskController->createTask($userId, $title, $description,$status, $categoryId, $priority,  $deadline);
        echo json_encode(['success' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'failed to create a task']);
    }
    exit();
}

