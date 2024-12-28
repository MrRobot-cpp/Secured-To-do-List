<?php

require_once '../Model/PriorityInterface.php';

class HighPriority implements PriorityInterface {
    public function getPriorityLevel() {
        return 'High';
    }
}
?>