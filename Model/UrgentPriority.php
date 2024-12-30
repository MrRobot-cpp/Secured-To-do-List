<?php

require_once __DIR__ . '/PriorityInterface.php';

class UrgentPriority implements PriorityInterface {
    public function getPriorityLevel() {
        return 'Urgent';
    }
}
?>