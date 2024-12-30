<?php

require_once __DIR__ .'/PriorityInterface.php';

class HighPriority implements PriorityInterface {
    public function getPriorityLevel() {
        return 'High';
    }
}
?>