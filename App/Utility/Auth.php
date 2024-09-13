<?php

namespace App\Utility;

use App\Config;
use App\Models\User;

class Auth {

    public static function checkIfUserIsLoggedIn() {
        if (isset($_SESSION['user']))
        {
            $user = User::getUserById($_SESSION['user']['id']);
            if ($user != null){
                $_SESSION['user']['is_admin'] = $user['is_admin'];
                return true;
            }else {
                return false;
            }
        }
        if (isset($_COOKIE[Config::REMEMBER_COOKIE_NAME])){
            $user = User::getUserByHash($_COOKIE[Config::REMEMBER_COOKIE_NAME]);
            if($user == null){
                setcookie(Config::REMEMBER_COOKIE_NAME, '', time() - 1);
                return false;
            }
            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
                'is_admin' => $user['is_admin'],
            );
            return true;
        }
        return false;
    }
}