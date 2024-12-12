<?php
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
    public function createTask($userId, $title, $description, $status, $priority, $categoryId,   $deadline, $projectId) {
        return $this->taskModel->createTask($userId, $title, $description, $status,$priority,$categoryId,  $deadline, $projectId);
    }

    // Update task status (for dragging between columns)
    public function updateTaskStatus($taskId, $status) {
        return $this->taskModel->updateTaskStatus($taskId, $status);
    }

    // Update a task (full update)
    public function updateTask($taskId, $title, $description, $categoryId, $priority, $status, $deadline, $projectId) {
        return $this->taskModel->updateTask($taskId, $title, $description, $categoryId, $priority, $status, $deadline, $projectId);
    }

    // Delete a task
    public function deleteTask($taskId) {
        return $this->taskModel->deleteTask($taskId);
    }

    public function getAllTasksByUser($userId) {
        return $this->taskModel->getTasksByUserId($userId);
    }
    public function getAllTasks() {
        return $this->taskModel->getAllTasks(); // Call the new method
    }
    public function getTaskCountByStatus($status) {
        return $this->taskModel->getTaskCountByStatus($status);
    }

    
    public function completionPercentage($totalTasks, $completedTasks) {
        if ($totalTasks === 0) {
            return 0; // Avoid division by zero
        }
        return ($completedTasks / $totalTasks) * 100;
    }
    public function getTopFiveUsers() {
        return $this->taskModel->getTopFiveUsersByTasks();
    }
    public function getTaskCountsByUser($userId) {
        return $this->taskModel->getTaskCountsByUser($userId);
    }
    public function getTasksByProjectId($projectId){
        return $this->taskModel->getTasksByProjectId($projectId);
    }



}  
// Handle the POST request for task creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    require_once '../Model/Database.php';
    require_once '../Controller/UserController.php';

    $db = (new Database())->getConnection();
    $taskController = new TaskController($db);
    $user = new UserController($db);

    $cat = ["urgent" => 1, "high" => 2, "normal" => 3];

    if ($_POST['priority'] === 'urgent') {
        $priorityid = $cat["urgent"];
        $namedprio = "urgent";
    } else if ($_POST['priority'] === 'normal') {
        $priorityid = $cat["normal"];
        $namedprio = "normal";
    } else {
        $priorityid = $cat["high"];
        $namedprio = "high";
    }

    $userId = $user->get_id($_SESSION["email"]);

    // Access the project_id from the POST data
    $projectId = $_POST['project_id'] ?? null;

    if ($projectId === null) {
        echo json_encode(['success' => false, 'message' => 'Project ID is missing.']);
        exit();
    }

    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $priority = $namedprio ?? 'normal';
    $status = $_POST['status'] ?? 'normal';
    $categoryId = $priorityid;
    $deadline = $_POST['deadline'] ?? null;

    if (!empty($title) || $userId > 0) {
        $result = $taskController->createTask($userId, $title, $description, $status, $priority, $categoryId, $deadline, $projectId);
        echo json_encode(['success' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create a task']);
    }
    exit();
}
