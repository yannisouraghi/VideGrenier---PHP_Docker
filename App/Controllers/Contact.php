<?php

namespace App\Controllers;

use \Core\View;

class Contact{
    public function contactAction()
    {
        View::renderTemplate('Contact/contact.html');
    }
}