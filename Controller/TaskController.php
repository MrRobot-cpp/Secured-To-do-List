<?php

require_once '../Model/Task.php';

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
    public function createTask($userId, $title, $description, $categoryId, $priority, $deadline) {
        return $this->taskModel->createTask($userId, $title, $description, $categoryId, $priority, $deadline);
    }

    // Update task status (for dragging between columns)
    public function updateTaskStatus($taskId, $status) {
        return $this->taskModel->updateTaskStatus($taskId, $status);
    }

    // Delete a task
    public function deleteTask($taskId) {
        return $this->taskModel->deleteTask($taskId);
    }
}
