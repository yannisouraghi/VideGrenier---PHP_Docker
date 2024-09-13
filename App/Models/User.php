<?php

namespace App\Models;

use App\Utility\Hash;
use Core\Model;
use App\Core;
use Exception;
use App\Utility;

/**
 * User Model:
 */
class User extends Model {

    /**
     * Crée un utilisateur
     */
    public static function createUser($data) {
        $db = static::getDB();
        $stmt = $db->prepare('INSERT INTO users(username, email, password, salt) VALUES (:username, :email, :password,:salt)');

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':salt', $data['salt']);

        $stmt->execute();
        return $db->lastInsertId();
    }

    public static function getByLogin($login)
    {
        $db = static::getDB();

        $stmt = $db->prepare("
            SELECT * FROM users WHERE (email = :email) LIMIT 1
        ");
        $stmt->bindParam(':email', $login);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // TODO
    public static function getUserCookiesById($userID)
    {
        $db = static::getDB();

        $stmt = $db->prepare("
            SELECT user_cookies FROM users WHERE id = :id
        ");

        $stmt->bindParam(':id', $userID);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC)["user_cookies"];
    }

    public static function deleteUserCookieByHash($hash){
        $db = static::getDB();
        $stmt = $db->prepare("update users set user_cookies = null WHERE user_cookies = :hash");
        $stmt->bindParam(':hash', $hash);
        $stmt->execute();
    }

    public static function getUserByHash($hash){
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE user_cookies = :hash LIMIT 1");
        $stmt->bindParam(':hash', $hash);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getUserById($userid){
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :userid");
        $stmt->bindParam(':userid', $userid);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function login() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT * FROM articles WHERE articles.id = ? LIMIT 1');

        $stmt->execute([$id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function addCookieToUserId($hash, $userID) {
        $db = static::getDB();
        $stmt = $db->prepare("update users set user_cookies = :hash where id = :id");
        $stmt->bindParam(':hash', $hash);
        $stmt->bindParam(':id', $userID);
        $stmt->execute();
    }

    public static function getCountAll(){
        $db = static::getDB();
        $stmt = $db->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function getAll(){
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getTodaysUser(){
        $db = static::getDB();
        $stmt = $db->prepare("SELECT COUNT(*) FROM visits where date = CURRENT_DATE");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function addTodaysUser(){
        $db = static::getDB();
        $stmt = $db->prepare("INSERT INTO visits(date) VALUES(CURRENT_DATE)");
        $stmt->execute();
    }

    public static function getEmailbyUserId($id) {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT email FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}

