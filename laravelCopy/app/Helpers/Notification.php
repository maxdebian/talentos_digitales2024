<?php

namespace App\Helpers;

class Notification
{
    public static function Notification($message, $alert)
    {
        return $notification = array(
            'message' => $message,
            'alert-type' => $alert
        );
    }
}
