<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\User;
use Core\Controller;
use \Core\View;

class Contact extends Controller{
    public function contactAction()
    {
        $id = $this->route_params['id'];

        try {
            Articles::addOneView($id);
            $article = Articles::getOne($id);
            $toEmail = User::getEmailbyUserId($article[0]['user_id']);
        } catch(\Exception $e){
            var_dump($e);
        }
        View::renderTemplate('Contact/contact.html', [
            'article' => $article[0],
            'toEmail' => $toEmail['email'],
        ]);
    }
}