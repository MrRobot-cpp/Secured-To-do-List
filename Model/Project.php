<?php
class Project {
    private $conn;
    private $table = 'projects';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new project
    public function createProject($userId, $name, $taskId = null) {
        $sql = "INSERT INTO {$this->table} (user_id, name) 
                VALUES (:user_id, :name)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':name', $name);

        return $stmt->execute();
    }

    public function updateProject($projectId, $name) {
        $sql = "UPDATE {$this->table} SET 
                name = :name 
                WHERE id = :projectId";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':projectId', $projectId);
        return $stmt->execute();
    }
    

    // Delete a project
    public function deleteProject($projectId) {
        $sql = "DELETE FROM {$this->table} WHERE id = :projectId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':projectId', $projectId);

        return $stmt->execute();
    }

    // Get projects by user ID
    public function getProjectsByUserId($userId) {
        $sql = "SELECT projects.*, users.name as user_name FROM {$this->table} 
                LEFT JOIN users ON projects.user_id = users.id
                WHERE user_id = :user_id 
                ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all projects
    public function getAllProjects() {
        $query = "SELECT projects.*, users.name as user_name FROM {$this->table}
                  LEFT JOIN users ON projects.user_id = users.id
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get task count by project
    public function getTaskCountByProject($projectId) {
        $query = "SELECT COUNT(tasks.id) as task_count 
                  FROM tasks 
                  WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $projectId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['task_count'];
    }

    // Get top 5 projects with the most tasks
    public function getTopFiveProjectsByTasks() {
        $query = "SELECT projects.id, projects.name, COUNT(tasks.id) as task_count 
                  FROM {$this->table}
                  LEFT JOIN tasks ON projects.id = tasks.project_id
                  GROUP BY projects.id, projects.name
                  ORDER BY task_count DESC
                  LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProjectById($projectId) {
        $sql = "SELECT * FROM projects WHERE id = :project_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public function searchProjects($userId, $searchTerm) {
        $stmt = $this->conn->prepare("SELECT * FROM projects WHERE user_id = :user_id AND name LIKE :search_term");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindValue(':search_term', '%' . $searchTerm . '%'); 
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
   
}
