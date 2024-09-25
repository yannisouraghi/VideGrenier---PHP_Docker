<?php

namespace App\Controllers;

use Exception;
use Core\View;
use Core\Controller;
use App\Config;
use App\Models\Articles;
use App\Utility\Hash;
use App\Utility\Auth;
use App\Utility\Flash;

/**
 * User controller
 */
class User extends Controller
{

    /**
     * Render the login page
     * @return void
     * @throws Exception
     */
    public function loginAction(): void {
        if(isset($_POST['submit'])){
            // Perform login
            $login = $this->login();

            // If user logged in, redirect
            if($login){
                header('Location: /account');
            }
        }

        View::renderTemplate('User/login.html', [
            'message' => Flash::getMessage()
        ]);
    }

    /**
     * Render the register page
     * @return void
     * @throws Exception
     */
    public function registerAction(): void {
        try {
            if(isset($_POST['submit'])){
                // Perform register
                $register = $this->register();

                $login = false;
                if($register) {
                    // Login with same information
                    $login = $this->login();
                }

                // If logged in, redirect
                if($register && $login) {
                    header('Location: /account');
                }
            }

            View::renderTemplate('User/register.html', [
                'message' => Flash::getMessage(),
                'data' => $_POST
            ]);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

    /**
     * Render the account page
     * @return void
     * @throws Exception
     */
    public function accountAction(): void {
        $articles = Articles::getByUser($_SESSION['user']['id']);

        View::renderTemplate('User/account.html', [
            'articles' => $articles
        ]);
    }

    /**
     * Register the user
     * @return boolean
     */
    private function register(): bool {
        try {
            // Check that username is correct
            if(empty($_POST['username']) || strlen($_POST['username']) < 3 || strlen($_POST['username']) > 100) {
                Flash::danger('Le nom d\'utilisateur est obligatoire. Il doit au moins contenir entre 3 et 100 caractères. ');
                return false;
            }

            // Check if email is correct
            if(empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || strlen($_POST['email']) > 254) {
                Flash::danger('L\'email est incorrect');
                return false;
            }

            // Check that password is correct
            if(!isset($_POST['password']) || strlen($_POST['password']) < 3) {
                Flash::danger('Le mot de passe doit au moins contenir 3 caractères');
                return false;
            }

            // Check that confirm password is correct
            if($_POST['password'] !== $_POST['password-check']){
                Flash::danger('Les mots de passe ne correspondent pas');
                return false;
            }

            // Check if user already exists
            $user = \App\Models\User::getByLogin($_POST['email']);

            if($user) {
                Flash::danger('Cet email est déjà utilisé');
                return false;
            }

            $salt = Hash::generateSalt(32);

            \App\Models\User::createUser([
                "email" => $_POST['email'],
                "username" => $_POST['username'],
                "password" => Hash::generate($_POST['password'], $salt),
                "salt" => $salt,
            ]);

            return true;
        } catch (Exception $e) {
            Flash::danger('Erreur lors de la création de l\'utilisateur');
            return false;
        }
    }

    /**
     * Login the user
     * @return boolean
     */
    private function login(): bool {
        try {
            if(!isset($_POST['email'])){
                Flash::danger('L\'email est manquante');
                return false;
            }

            if(!isset($_POST['password'])){
                Flash::danger('Le mot de passe est manquant');
                return false;
            }

            $user = \App\Models\User::getByLogin($_POST['email']);

            if (!$user || Hash::generate($_POST['password'], $user['salt']) !== $user['password']) {
                Flash::danger('Identifiants incorrects');
                return false;
            }

            $checked = false;

            if (isset($_POST['remember']) && $_POST['remember'] == "on") {
                $checked = true;
            }

            if ($checked and !self::createRememberCookie($user['id'])) {
                throw new Exception();
            }

            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
                'is_admin' => $user['is_admin'],
            );

            return true;
        } catch (Exception $e) {
            Flash::danger('Une erreur inconnue s\'est produite');
            return false;
        }
    }

    /**
     * Create the remember me cookie
     * @param $userID
     * @return boolean
     */
    private function createRememberCookie($userID): bool {
        try {
            $check = \App\Models\User::getUserCookiesById($userID);

            if ($check) {
                $hash = $check;
            } else {
                $hash = Hash::generateUnique();
                \App\Models\User::addCookieToUserId($hash, $userID);
            }

            setcookie(Config::REMEMBER_COOKIE_NAME, $hash, time() + Config::REMEMBER_COOKIE_EXPIRY, "/");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Logout: Delete cookie and session. Returns true if everything is okay,
     * otherwise turns false.
     * @return boolean
     */
    public function logoutAction(): bool {
        // Check if user is logged in
        if(!Auth::checkIfUserIsLoggedIn()) {
            return false;
        }

        // If the user has a remember me cookie, delete it
        if (isset($_COOKIE[Config::REMEMBER_COOKIE_NAME])){
            $hash = $_COOKIE[Config::REMEMBER_COOKIE_NAME];
            \App\Models\User::deleteUserCookieByHash($hash);
            setcookie(Config::REMEMBER_COOKIE_NAME, '', time() - 1);
        }

        // Reset session
        unset($_SESSION['user']);

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        header ("Location: /");
        return true;
    }
}
