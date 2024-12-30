
<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

require_once __DIR__ . '/../../Controller/TaskController.php';
require_once __DIR__ . '/../../Model/Task.php';

class TaskControllerTest extends TestCase {
    /**
     * @var MockObject
     */
    private $taskModelMock;

    protected function setUp(): void {
        $this->taskModelMock = $this->createMock(Task::class);
    }

    public function testUpdateTask() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['action'] = 'update_task';
        $_POST['task_id'] = 1;
        $_POST['title'] = 'Test Task';
        $_POST['description'] = 'Test Description';
        $_POST['status'] = 'pending';
        $_POST['priority'] = 'high';
        $_POST['category_id'] = 2;
        $_POST['project_id'] = 3;
        $_POST['deadline'] = '2023-12-31';

        $this->taskModelMock->expects($this->once())
            ->method('updateTask')
            ->with(
                $this->equalTo(1),
                $this->equalTo('Test Task'),
                $this->equalTo('Test Description'),
                $this->equalTo('pending'),
                $this->equalTo('high'),
                $this->equalTo(2),
                $this->equalTo('2023-12-31'),
                $this->equalTo(3)
            );

        $taskController = new TaskController($this->taskModelMock);
        $taskController->updateTask(
            $_POST['task_id'],
            $_POST['title'],
            $_POST['description'],
            $_POST['status'],
            $_POST['priority'],
            $_POST['category_id'],
            $_POST['deadline'],
            $_POST['project_id']
        );
    }
}
?>