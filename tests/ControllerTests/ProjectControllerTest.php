
<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../Controller/ProjectController.php';
require_once __DIR__ . '/../../Model/Project.php';

class ProjectControllerTest extends TestCase
{
    protected $projectController;

    protected function setUp(): void
    {
        $this->projectController = new ProjectController(new Project('Project Name'));
    }

    public function testIndex()
    {
        $response = $this->projectController->index();
        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    public function testShow()
    {
        $projectId = 1; // Assuming a project with ID 1 exists
        $response = $this->projectController->show($projectId);
        $this->assertInstanceOf(Project::class, $response);
    }

    public function testStore()
    {
        $projectData = [
            'name' => 'Test Project',
            'description' => 'This is a test project'
        ];
        $response = $this->projectController->store($projectData);
        $this->assertTrue($response);
    }

    public function testUpdate()
    {
        $projectId = 1; // Assuming a project with ID 1 exists
        $projectData = [
            'name' => 'Updated Project',
            'description' => 'This is an updated test project'
        ];
        $response = $this->projectController->update($projectId, $projectData);
        $this->assertTrue($response);
    }

    public function testDestroy()
    {
        $projectId = 1; // Assuming a project with ID 1 exists
        $response = $this->projectController->destroy($projectId);
        $this->assertTrue($response);
    }
}