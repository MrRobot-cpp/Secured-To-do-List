
<?php
use PHPUnit\Framework\TestCase;


require_once __DIR__ . '/../../Model/Database.php';
require_once __DIR__ . '/../../Model/Task.php';

class TaskTest extends TestCase {
    private $db;
    private $task;

    protected function setUp(): void {
        $this->db = Database::getInstance()->getConnection();
        $this->task = new Task($this->db);
    }

    public function testCreateTask() {
        $result = $this->task->createTask(4, 'Test Task', 'This is a test task', 'to_do', 'high', 1, '2024-12-31', 17);
        $this->assertTrue($result);
    }

    public function testUpdateTask() {
        $result = $this->task->updateTask(60, 'Updated Task', 'This is an updated test task', 'urgent', '2024-12-31');
        $this->assertTrue($result);
    }

    public function testUpdateTaskStatus() {
        $result = $this->task->updateTaskStatus(60, 'finished');
        $this->assertTrue($result);
    }

    public function testGetTasksByUserId() {
        $tasks = $this->task->getTasksByUserId(1);
        $this->assertIsArray($tasks);
    }

    public function testDeleteTask() {
        $result = $this->task->deleteTask(1);
        $this->assertTrue($result);
    }

    public function testGetAllTasks() {
        $tasks = $this->task->getAllTasks();
        $this->assertIsArray($tasks);
    }

    public function testGetTaskCountByStatus() {
        $count = $this->task->getTaskCountByStatus('to_do');
        $this->assertIsInt($count);
    }

    public function testGetTaskCountByStatusPerUser() {
        $count = $this->task->getTaskCountByStatusPerUser('to_do', 1);
        $this->assertIsInt($count);
    }

    public function testGetTaskCountByStatusPerProject() {
        $count = $this->task->getTaskCountByStatusPerProject('to_do', 1);
        $this->assertIsInt($count);
    }

    public function testGetTaskCountByProjectId() {
        $count = $this->task->getTaskCountByProjectId(1);
        $this->assertIsInt($count);
    }

    public function testGetTaskCountsByUser() {
        $counts = $this->task->getTaskCountsByUser(1);
        $this->assertIsArray($counts);
    }

    public function testGetTopFiveUsersByTasks() {
        $users = $this->task->getTopFiveUsersByTasks();
        $this->assertIsArray($users);
    }

    public function testGetTasksByProjectId() {
        $tasks = $this->task->getTasksByProjectId(1);
        $this->assertIsArray($tasks);
    }
}
?>