<?php

require_once '../Model/UrgentPriority.php';
require_once '../Model/NormalPriority.php';
require_once '../Model/HighPriority.php';

class PriorityFactory {
    public static function createPriority($priority) {
        switch (strtolower($priority)) {
            case 'urgent':
                return new UrgentPriority();
            case 'high':
                return new HighPriority();
            case 'normal':
            default:
                return new NormalPriority();
        }
    }
}
?>