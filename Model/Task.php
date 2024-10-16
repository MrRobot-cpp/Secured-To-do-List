<?php
class Task {
    private $conn;
    private $table = 'tasks';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new task
    public function createTask($userId, $title, $description, $categoryId, $priority, $deadline) {
        $sql = "INSERT INTO {$this->table} (user_id, title, description, category_id, priority, deadline) VALUES (:user_id, :title, :description, :category_id, :priority, :deadline)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':deadline', $deadline);

        return $stmt->execute();
    }

    public function updateTask($taskId, $title, $description, $categoryId, $priority, $status, $deadline) {
        $sql = "UPDATE tasks SET 
                title = :title, 
                description = :description, 
                category_id = :category_id, 
                priority = :priority, 
                status = :status, 
                deadline = :deadline 
                WHERE id = :taskId";
        $stmt = $this->conn->prepare($sql);
    
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':deadline', $deadline);
        $stmt->bindParam(':taskId', $taskId);
    
        return $stmt->execute();
    }
    
    public function updateTaskStatus($taskId, $status) {
        $sql = "UPDATE tasks SET status = :status WHERE id = :taskId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':taskId', $taskId);
    
        return $stmt->execute();
    }

    public function getTasksByUserId($userId) {
        $sql = "SELECT tasks.*, categories.name as category_name FROM tasks 
                LEFT JOIN categories ON tasks.category_id = categories.id
                WHERE user_id = :user_id 
                ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteTask($taskId) {
        $sql = "DELETE FROM tasks WHERE id = :taskId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':taskId', $taskId);
    
        return $stmt->execute();
    }
}
