<?php
namespace App\Helpers;
use DateTime;

class CommonHelper {
    public static function generateUniqueId() {
        $now = new DateTime();
        return (string)(bin2hex(random_bytes(4)) .''. $now->getTimestamp());
    }

    public static function getCurrentDateTime() {
        $now = new DateTime();
        return $now->format('Y-m-d H:i:s');
    }
}
