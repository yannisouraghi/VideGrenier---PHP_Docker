<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

use App\Utility\Flash;

session_start();

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';

ini_set('display_errors', '1');


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'User', 'action' => 'login']);
$router->add('register', ['controller' => 'User', 'action' => 'register']);
$router->add('logout', ['controller' => 'User', 'action' => 'logout', 'private' => true]);
$router->add('account', ['controller' => 'User', 'action' => 'account', 'private' => true]);
$router->add('product', ['controller' => 'Product', 'action' => 'index', 'private' => true]);
$router->add('product/{id:\d+}', ['controller' => 'Product', 'action' => 'show']);
$router->add('admin', ['controller' => 'Admin', 'action' => 'admin', 'admin_only' => true]);
$router->add('product/{id:\d+}/contact', ['controller' => 'Contact', 'action' => 'contact']);
$router->add('{controller}/{action}');

/*
 * Gestion des erreurs dans le routing
 */
try {
    $router->dispatch($_SERVER['QUERY_STRING']);
} catch(Exception $e){
    switch($e->getMessage()){
        case 'You must be logged in':
            Flash::danger($e -> getMessage());
            header('Location: /login');
            break;
        default:
            header('Location: /');
            break;
    }
}
