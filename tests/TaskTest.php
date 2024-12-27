<?php
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskCreation()
    {
        // Assuming Task is a class with a constructor that takes a title
        $task = new Task("Finish the project");

        // Use assertions to verify the behavior
        $this->assertEquals("Finish the project", $task->getTitle());
    }
}
