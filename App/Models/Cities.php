<?php

namespace App\Models;

use Core\Model;

/**
 * City Model:
 */
class Cities extends Model {
    public static function search($str) {
        $db = static::getDB();

        $str = strtolower($str);

        $stmt = $db->prepare('SELECT ville_id,ville_code_postal,ville_nom_reel FROM villes_france WHERE ville_nom_simple LIKE :query');

        $query = $str . '%';

        $stmt->bindParam(':query', $query);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM villes_france WHERE ville_id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_UNIQUE);
    }
}
