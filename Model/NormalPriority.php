<?php

require_once __DIR__ .'/PriorityInterface.php';

class NormalPriority implements PriorityInterface {
    public function getPriorityLevel() {
        return 'Normal';
    }
}
?>