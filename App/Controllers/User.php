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

            // TODO: Validation
            
            $this->login($f);

            // Si login OK, redirige vers le compte
            header('Location: /account');
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
                "salt" => $salt
            ]);

            return $userID;

        } catch (Exception $ex) {
            // TODO : Set flash if error : utiliser la fonction en dessous
            /* Utility\Flash::danger($ex->getMessage());*/
            Flash::danger('Erreur lors de la création de l\'utilisateur');
        }
    }

    private function login($data){
        try {
            if(!isset($data['email'])){
                throw new InvalidArgumentException('Email manquant');
            }
            if(!isset($data['password'])){
                throw new InvalidArgumentException('Mot de passe manquant');
            }
            $user = \App\Models\User::getByLogin($data['email']);
            var_dump($user);
            if (!$user || Hash::generate($data['password'], $user['salt']) !== $user['password']) {
                return false;
            }
            // TODO: Create a remember me cookie if the user has selected the option
            // to remained logged in on the login form.
            // https://github.com/andrewdyer/php-mvc-register-login/blob/development/www/app/Model/UserLogin.php#L86
            $remember = self::post("remember") ? true : false;
            if ($remember and !self::createRememberCookie($user['id'])) {
                throw new Exception("Une erreur est survenue.");
            }
            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
            );

            return true;

        } catch (Exception $ex) {
            // TODO : Set flash if error
            /* Utility\Flash::danger($ex->getMessage());*/
            Flash::danger($ex -> getMessage());
        }
    }

    public static function createRememberCookie($userID) {
        $check = \App\Models\User::getUserCookiesById($userID);
        if ($check) {
            $hash = $check['user_cookies'];
        } else {
            $hash = Utility\Hash::generateUnique();
            if (!$Db->insert("user_cookies", ["id" => $userID, "user_cookies" => $hash])) {
                return false;
            }
        }
        //$cookie = $_COOKIE("COOKIE_USER");
        $cookie = Config::COOKIE_DEFAULT_EXPIRY;
        //$expiry = get("COOKIE_DEFAULT_EXPIRY");
        $expiry = Config::COOKIE_USER;
        return(setcookie($cookie, $hash, time() + $expiry, "/"));
    }

    public static function LoginWithCookies(){
        $cookie = get("COOKIE_USER");
        if (isset($_COOKIE[$cookie])) {
            $Db = static::getDB();
            $check = $Db->select("user_cookies", ["hash", "=", $_COOKIE[$cookie]]);
            if ($check->count()) {
                $user = $Db->select("users", ["id", "=", $check->first()->id]);
                if ($user->count()) {
                    $_SESSION['user'] = array(
                        'id' => $user->first()->id,
                        'username' => $user->first()->username,
                    );
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Logout: Delete cookie and session. Returns true if everything is okay,
     * otherwise turns false.
     * @access public
     * @return boolean
     * @since 1.0.2
     */
    public function logoutAction() {

        /*
        if (isset($_COOKIE[$cookie])){
            // TODO: Delete the users remember me cookie if one has been stored.
            // https://github.com/andrewdyer/php-mvc-register-login/blob/development/www/app/Model/UserLogin.php#L148
        }*/
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
