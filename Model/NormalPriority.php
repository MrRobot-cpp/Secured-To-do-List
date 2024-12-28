<?php

require_once '../Model/PriorityInterface.php';

class NormalPriority implements PriorityInterface {
    public function getPriorityLevel() {
        return 'Normal';
    }
}
?>