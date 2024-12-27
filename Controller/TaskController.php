<?php
require_once '../Model/Task.php';
require_once 'UserController.php';
require_once '../Model/Database.php';
require_once __DIR__ . '\PHPMailer-master\src\Exception.php';
require_once __DIR__ . '\PHPMailer-master\src\PHPMailer.php';
require_once __DIR__ . '\PHPMailer-master\src\SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



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
    public function updateTask($taskId, $title, $description,  $priority,  $deadline, ) {
        return $this->taskModel->updateTask($taskId, $title, $description,  $priority,  $deadline);
    }

    // Delete a task
    public function deleteTask($taskId) {
        return $this->taskModel->deleteTask($taskId);
    }
    public function Deadline($deadline,$email){
        $deadlineDate = new DateTime($deadline);
        $currentDate = new DateTime();
    
        $interval = $currentDate->diff($deadlineDate);
        $daysRemaining = (int)$interval->format('%r%a'); 
        if ($daysRemaining < 0) {
            $subject = "Deadline Passed!";
            $body = "The deadline on " . $deadlineDate->format('Y-m-d H:i:s') . " has passed.";
        } elseif ($daysRemaining <= 1) {
            $subject = "Deadline Approaching!";
            $body = "The deadline is near! You have less than " . $daysRemaining . " day(s) left until " . $deadlineDate->format('Y-m-d H:i:s') . ".";
        } else {
            return; 
        }
    
        $mail=new PHPMailer(true);
        $miamail="wandenreich111@gmail.com";
        $mianame="kanaban@nonreply";
        $miapassword="azehhtmxgxtevpgc";
        
        try {
            $mail->SMTPDebug=SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host='smtp.gmail.com';
            $mail->SMTPAuth=true;
            $mail->Username=$miamail;
            $mail->Password=$miapassword;
            $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port=587;
        
            $mail->setFrom($miamail,$mianame);
            $mail->addAddress($email);
            $mail->Subject=$subject;
            $mail->Body=$body;
            $mail->send();
            return true;
        
        
        } catch (Exception $e) {
            echo"mailing error".$e->getMessage();
       echo "error in sending the mail".$mail->ErrorInfo;
            
            return false;
        }
        
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = (new Database())->getConnection();
    $taskController = new TaskController($db);

    if (isset($_POST['action']) && $_POST['action'] === 'update_task') {
        $taskId = $_POST['task_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $priority = $_POST['priority'];
        $status = $_POST['status'];
        $deadline = $_POST['deadline'];

if($taskController->Deadline($deadline,$_SESSION["email"])){
    header("Location: ../View/kanban.php?project_id=" . $_POST['project_id']);
}

        if ($taskController->updateTask($taskId, $title, $description,  $priority,  $deadline)) {
            header("Location: ../View/kanban.php?project_id=" . $_POST['project_id']);
            exit();
        } else {
            echo "Failed to update task.";
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = (new Database())->getConnection();
    $taskController = new TaskController($db);

    if (isset($_POST['action']) && $_POST['action'] === 'delete_task') {
        $taskId = $_POST['task_id'];

        if ($taskController->deleteTask($taskId)) {
            header("Location: ../View/kanban.php?project_id=" . $_POST['project_id']);
            exit();
        } else {
            echo "Failed to delete task.";
        }
    }
}
// Handle the POST request for task creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    //require_once '../Model/Database.php';
    require_once '../Controller/UserController.php';

    $db = (new Database())->getConnection();
    $taskController = new TaskController($db);
    $user = new UserController($db);

    $cat = ["urgent" => 1, "high" => 2, "normal" => 3];
    $statusOptions = ["todo" => "To do", "inprogress" => "inprogress", "finished" => "finished"];

    if ($_POST['priority'] === 'urgent') {
        $priorityid = $cat["urgent"];
        $namedprio = "urgent";
    } elseif ($_POST['priority'] === 'normal') {
        $priorityid = $cat["normal"];
        $namedprio = "normal";
    } else{
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
    $status = $_POST['status'] ?? 'todo';
    $categoryId = $priorityid;
    $deadline = $_POST['deadline'] ?? null;
    $status = $_POST['status'] ?? 'todo';
    if (!in_array($status, ['todo', 'inprogress', 'finished'])) {
        $status = 'todo'; // Fallback if an invalid status is provided
    }
    if($taskController->Deadline($deadline,$_SESSION["email"])){
        echo json_encode(['success' => false, 'message' => 'Deadline is near']);
        exit();
    }
    
    if (!empty($title) || $userId > 0) {
        $result = $taskController->createTask($userId, $title, $description, $status, $priority, $categoryId, $deadline, $projectId);

        echo json_encode(['success' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create a task']);
    }
    exit();
}