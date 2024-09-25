<?php

namespace App\Utility;

use App\Config;
use App\Models\User;

class Auth {

    public static function checkIfUserIsLoggedIn(): array | null {
        if (isset($_SESSION['user'])) {
            $user = User::getUserById($_SESSION['user']['id']);
            if ($user != null){
                $_SESSION['user']['is_admin'] = $user['is_admin'];
                return $user;
            }else {
                return null;
            }
        }

        if (isset($_COOKIE[Config::REMEMBER_COOKIE_NAME])){
            $user = User::getUserByHash($_COOKIE[Config::REMEMBER_COOKIE_NAME]);

            if($user == null){
                setcookie(Config::REMEMBER_COOKIE_NAME, '', time() - 1);
                return null;
            }

            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
                'is_admin' => $user['is_admin'],
            );

            return $user;
        }

        return null;
    }

    public static function checkIfUserIsAdmin() {
        $user = self::checkIfUserIsLoggedIn();
        if(!$user) return null;
        return $user['is_admin'];
    }
}