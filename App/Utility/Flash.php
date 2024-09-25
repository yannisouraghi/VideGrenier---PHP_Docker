<?php 

namespace App\Utility;

class Flash{

    public static function addMessage($message, $type = 'success'){
            $_SESSION['flash_notifications'] = [
            'body' => $message,
            'type' => $type
        ];
    }

    public static function getMessage(){
        if(isset($_SESSION['flash_notifications'])){
            $messages = $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);
            return $messages;
        }

        return null;
    }

    public static function danger($message){
        self::addMessage($message, 'danger');
    }
}