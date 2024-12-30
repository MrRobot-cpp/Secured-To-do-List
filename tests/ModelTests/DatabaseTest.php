<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../Model/Database.php';

class DatabaseTest extends TestCase {
    public function testGetInstanceReturnsSameInstance() {
        $db1 = Database::getInstance();
        $db2 = Database::getInstance();
        $this->assertSame($db1, $db2);
    }

    public function testGetConnectionReturnsPDOInstance() {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $this->assertInstanceOf(PDO::class, $conn);
    }

    public function testConnectionIsNotNull() {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $this->assertNotNull($conn);
    }

    public function testConnectionHasCorrectAttributes() {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $this->assertEquals(PDO::ERRMODE_EXCEPTION, $conn->getAttribute(PDO::ATTR_ERRMODE));
    }
}
?>