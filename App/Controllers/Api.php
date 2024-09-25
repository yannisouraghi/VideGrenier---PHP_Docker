<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use \Core\View;
use Exception;

/**
 * API controller
 */
class Api extends \Core\Controller
{

    /**
     * Affiche la liste des articles / produits pour la page d'accueil
     * @throws Exception
     */
    public function ProductsAction()
    {
        $query = $_GET['sort'];

        $articles = Articles::getAll($query);

        header('Content-Type: application/json');
        echo json_encode($articles);
    }

    /**
     * Recherche dans la liste des villes
     * @throws Exception
     */
    public function CitiesAction(){
        try {
            $cities = Cities::search($_GET['query']);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

        header('Content-Type: application/json');
        echo json_encode($cities);
    }

    /**
     * Recherche des articles
     * @throws Exception
     */
    public function SearchAction(){

        try {

            $query = $_GET['param'];

            $articles = Articles::getByName($query);

            header('Content-Type: application/json');

            echo json_encode($articles);

        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }
}
