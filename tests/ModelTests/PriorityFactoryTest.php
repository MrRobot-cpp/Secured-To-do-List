
<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../Model/PriorityFactory.php';
require_once __DIR__ . '/../../Model/UrgentPriority.php';
require_once __DIR__ . '/../../Model/NormalPriority.php';
require_once __DIR__ . '/../../Model/HighPriority.php';

class PriorityFactoryTest extends TestCase {
    public function testCreateUrgentPriority() {
        $priority = PriorityFactory::createPriority('urgent');
        $this->assertInstanceOf(UrgentPriority::class, $priority);
    }

    public function testCreateHighPriority() {
        $priority = PriorityFactory::createPriority('high');
        $this->assertInstanceOf(HighPriority::class, $priority);
    }

    public function testCreateNormalPriority() {
        $priority = PriorityFactory::createPriority('normal');
        $this->assertInstanceOf(NormalPriority::class, $priority);
    }

    public function testCreateDefaultPriority() {
        $priority = PriorityFactory::createPriority('unknown');
        $this->assertInstanceOf(NormalPriority::class, $priority);
    }
}
?>