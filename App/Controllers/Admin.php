<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use App\Models\User;
use \Core\View;
use Exception;

/**
 * Admin controller
 */
class Admin extends \Core\Controller
{
    public function adminAction()
    {
        $nbArt = $this->nombreArticlesAction();
        $nbUsers = $this->nombreUsersAction();
        $nbTodaysUser = $this->nombreTodaysUserAction();
        $listUser = $this->listUsersAction();
        View::renderTemplate('Admin/admin.html', [
            'nbArt' => $nbArt,
            'nbUsers' => $nbUsers,
            'nbTodaysUser' => $nbTodaysUser,
            'listUser' => $listUser,
        ]);
    }

    public function nombreArticlesAction(){
        return Articles::getCountAll();
    }

    public function nombreUsersAction(){
        return User::getCountAll();
    }

    public function nombreTodaysUserAction(){
        return User::getTodaysUser();
    }

    public function listUsersAction(){
        $user = User::getAll();
        return $user;
    }
}
