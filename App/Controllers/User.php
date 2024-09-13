<?php

namespace App\Controllers;

use App\Config;
use App\Model\UserRegister;
use App\Models\Articles;
use App\Utility\Hash;
use App\Utility\Session;
use App\Utility\Flash;
use \Core\View;
use Exception;
use http\Env\Request;
use http\Exception\InvalidArgumentException;
use App\Utility\Input;
use App\Utility\Auth;

/**
 * User controller
 */
class User extends \Core\Controller
{

    /**
     * Affiche la page de login
     */
    public function loginAction()
    {
        if(isset($_POST['submit'])){
            $f = $_POST;
            
            $result = $this->login($f);
            // Si login OK, redirige vers le compte
            if($result){
                header('Location: /account');
            }
        }
        View::renderTemplate('User/login.html', [
            'message' => Flash::getMessage()
        ]);
    }

    /**
     * Page de création de compte
     */
    public function registerAction()
    {
        if(isset($_POST['submit'])){
            $f = $_POST;

            if($f['password'] !== $f['password-check']){
                throw new InvalidArgumentException('Les mots de passe ne correspondent pas');
            }

            // validation

            $this->register($f);
            // TODO: Rappeler la fonction de login pour connecter l'utilisateur
            $this->login($f);

            header('Location: /account');

        }
        View::renderTemplate('User/register.html', [
            'message' => Flash::getMessage()
        ]);
    }

    /**
     * Affiche la page du compte
     */
    public function accountAction()
    {
        $articles = Articles::getByUser($_SESSION['user']['id']);

        View::renderTemplate('User/account.html', [
            'articles' => $articles
        ]);
    }

    /*
     * Fonction privée pour enregister un utilisateur
     */
    private function register($data)
    {
        try {
            // Generate a salt, which will be applied to the during the password
            // hashing process.

            $salt = Hash::generateSalt(32);
            
            $userID = \App\Models\User::createUser([
                "email" => $data['email'],
                "username" => $data['username'],
                "password" => Hash::generate($data['password'], $salt),
                "salt" => $salt,
            ]);

            return $userID;

        } catch (Exception $ex) {
            // TODO : Set flash if error : utiliser la fonction en dessous
            /* Utility\Flash::danger($ex->getMessage());*/
            Flash::danger('Erreur lors de la création de l\'utilisateur');
        }
    }

    private function login($data): bool
    {
        try {
            if(!isset($data['email'])){
                Flash::danger('email manquant');
            }

            if(!isset($data['password'])){
                Flash::danger('mot de passe manquant');
                return false;
            }

            $user = \App\Models\User::getByLogin($data['email']);
            if (!$user || Hash::generate($data['password'], $user['salt']) !== $user['password']) {
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
        } catch (Exception $ex) {
            Flash::danger('Une erreur s\'est produite');
            return false;
        }
    }

    public static function createRememberCookie($userID): bool
    {
        try {
            $check = \App\Models\User::getUserCookiesById($userID);
            if ($check) {
                $hash = $check;
            } else {
                $hash = Hash::generateUnique();
                \App\Models\User::addCookieToUserId($hash, $userID);
            }
            return (setcookie(Config::REMEMBER_COOKIE_NAME, $hash, time() + Config::REMEMBER_COOKIE_EXPIRY, "/"));
        } catch (Exception $ex) {
            var_dump($ex);
            return false;
        }
    }


    /**
     * Logout: Delete cookie and session. Returns true if everything is okay,
     * otherwise turns false.
     * @access public
     * @return boolean
     * @since 1.0.2
     */
    public function logoutAction() {


        if (isset($_COOKIE[Config::REMEMBER_COOKIE_NAME])){
            $hash = $_COOKIE[Config::REMEMBER_COOKIE_NAME];
            try {
                \App\Models\User::deleteUserCookieByHash($hash);
            }
            catch (Exception $ex) {
                var_dump($ex);
            }
            setcookie(Config::REMEMBER_COOKIE_NAME, '', time() - 1);
        }
        // Destroy all data registered to the session.

        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header ("Location: /");

        return true;
    }
}
