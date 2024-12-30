<?php
require_once __DIR__ . '/UrgentPriority.php';
require_once __DIR__ . '/NormalPriority.php';
require_once __DIR__ . '/HighPriority.php';

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