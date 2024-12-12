<?php
require_once '../Model/Project.php';
require_once 'UserController.php';

class ProjectController {
    private $projectModel;

    public function __construct($db) {
        $this->projectModel = new Project($db);
    }

    // Display all projects
    public function showProjects($userId) {
        return $this->projectModel->getProjectsByUserId($userId);
    }

    // Create a new project
    public function createProject($userId, $name, $taskId = null) {
        return $this->projectModel->createProject($userId, $name, $taskId);
    }

    // Update a project
    public function updateProject($projectId, $name, $taskId = null) {
        return $this->projectModel->updateProject($projectId, $name, $taskId);
    }

    // Delete a project
    public function deleteProject($projectId) {
        return $this->projectModel->deleteProject($projectId);
    }

    // Get all projects
    public function getAllProjects() {
        return $this->projectModel->getAllProjects();
    }

    // Get a count of tasks in a project
    public function getTaskCountByProject($projectId) {
        return $this->projectModel->getTaskCountByProject($projectId);
    }
    public function getProjectById($projectId) {
        return $this->projectModel->getProjectById($projectId);
    }
    public function getProjectsByUserId($userId) {
        return $this->projectModel->getProjectsByUserId($userId);
    }
    
}

// Handle the POST request for adding a new project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
    session_start();
    require_once '../Model/Database.php';
    require_once '../Controller/UserController.php';

    $db = (new Database())->getConnection();
    $projectController = new ProjectController($db);

    // Get the user ID from the session and the project name from the form
    $userId = $_POST['user_id'] ?? 0;
    $name = $_POST['name'] ?? '';

    // Add the project if the name is provided and user is logged in
    if (!empty($name) && $userId > 0) {
        $result = $projectController->createProject($userId, $name);
        if ($result) {
            header("Location: kanban.php");  // Redirect back to the project dashboard after adding
            exit();
        } else {
            // Handle error if project could not be added
            echo "Failed to add project. Please try again.";
        }
    }
}

