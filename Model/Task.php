<?php
require_once __DIR__ . '/PriorityFactory.php';
class Task {
    private $conn;
    private $table = 'tasks';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new task
    public function createTask($userId, $title, $description, $status,$priority,$categoryId,  $deadline, $projectId) {
        $priorityObject = PriorityFactory::createPriority($priority);
        $priorityLevel = $priorityObject->getPriorityLevel();
        $sql = "INSERT INTO {$this->table} (user_id, title, description, status,priority, category_id,  deadline, project_id)
                VALUES (:user_id, :title, :description,:status, :priority, :category_id,  :deadline, :project_id)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':status',$status);
        $stmt->bindParam(':priority', $priorityLevel);
        $stmt->bindParam(':deadline', $deadline);
        $stmt->bindParam(':project_id', $projectId);


        return $stmt->execute();
    }

    public function updateTask($taskId, $title, $description,  $priority,  $deadline) {
        $priorityObject = PriorityFactory::createPriority($priority);
        $priorityLevel = $priorityObject->getPriorityLevel();
        $sql = "UPDATE tasks SET
                title = :title,
                description = :description,
               
                priority = :priority,
                
                deadline = :deadline
                WHERE id = :taskId";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
       // $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':priority', $priorityLevel);
       // $stmt->bindParam(':status', $status);
        $stmt->bindParam(':deadline', $deadline);
        //$stmt->bindParam(':project_id', $projectId);
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
    public function getAllTasks() {
        $query = "SELECT * FROM tasks";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTaskCountByStatus($status) {
        try {
            $query = "SELECT COUNT(*) as count FROM tasks WHERE status = :status";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return 0; // Return 0 in case of an error
        }
    }
    
    public function getTaskCountByStatusPerUser($status, $userId) {
        try {
            $query = "SELECT COUNT(*) as count FROM tasks WHERE status = :status AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return 0; // Return 0 in case of an error
        }
    }
    public function getTaskCountByStatusPerProject($status, $projectId) {
        try {
            $query = "SELECT COUNT(*) as count FROM tasks WHERE status = :status AND project_id = :project_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return 0; // Return 0 in case of an error
        }
    }
    public function getTaskCountByProjectId($projectId) {
        try {
            $query = "SELECT COUNT(*) as task_count FROM tasks WHERE project_id = :project_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result) {
                return (int)$result['task_count'];
            } else {
                return 0; // No tasks found
            }
        } catch (PDOException $e) {
            // Handle the exception (log it or display an error)
            error_log("Error fetching task count: " . $e->getMessage());
            return 0;
        }
    }
       
   
    public function getTaskCountsByUser($userId) {
        $sql = "SELECT 
                    COALESCE(SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END), 0) AS done,
                    COALESCE(SUM(CASE WHEN status = 'to_do' THEN 1 ELSE 0 END), 0) AS to_do,
                    COALESCE(SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END), 0) AS in_progress
                FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getTopFiveUsersByTasks() {
        $query = "SELECT users.id, users.name, COUNT(tasks.id) as task_count
                  FROM {$this->table} tasks
                  INNER JOIN users ON tasks.user_id = users.id
                  GROUP BY users.id, users.name
                  ORDER BY task_count DESC
                  LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTasksByProjectId($projectId) {
        $query = "SELECT id, title, description, category_id, priority, status, deadline, project_id
                  FROM tasks WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $projectId);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}