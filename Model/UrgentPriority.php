<?php

require_once '../Model/PriorityInterface.php';

class UrgentPriority implements PriorityInterface {
    public function getPriorityLevel() {
        return 'Urgent';
    }
}
?>