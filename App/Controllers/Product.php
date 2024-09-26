<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use App\Utility\Flash;
use App\Utility\Upload;
use \Core\View;

/**
 * Product controller
 */
class Product extends \Core\Controller
{
    public Cities $citiesModel;
    public Upload $uploadUtility;
    public Articles $articlesModel;

    public function __construct($route_params)
    {
        parent::__construct($route_params);
        $this->citiesModel = new Cities();
        $this->uploadUtility = new Upload();
        $this->articlesModel = new Articles();
    }

    public function setCitiesModel($model): void
    {
        $this->citiesModel = $model;
    }

    public function setUploadUtility($uploadUtility): void
    {
        $this->uploadUtility = $uploadUtility;
    }

    public function setArticlesModel($articlesModel): void
    {
        $this->articlesModel = $articlesModel;
    }

    /**
     * Affiche la page d'ajout
     * @return void
     */
    public function indexAction(): void
    {

        if(isset($_POST['submit'])) {
            $result = self::addProduct();

           if($result){
               return;
           }
        }

        View::renderTemplate('Product/Add.html', [
            'message' => Flash::getMessage()
        ]);
    }

    public function addProduct(): bool
    {
        try {
            $validation_errors = [];

            if(empty($_POST['name']) || $_POST['name'] == '' || strlen($_POST['name']) > 200) {
                $validation_errors[] = 'Le nom est requis et ne peut pas dépasser 200 caractères';
            }

            if(empty($_POST['description']) || $_POST['description'] == '') {
                $validation_errors[] = 'La description est requise';
            }

            if(empty($_POST['city_id']) || $_POST['city_id'] == '') {
                $validation_errors[] = 'La ville est requise';
            }

            if(empty($_FILES['picture'])) {
                $validation_errors[] = "L'image est requis";
            }

            if (count($validation_errors) > 0) {
                $validation_errors_string = "Une ou plusieurs erreurs sont survenues : \n";

                foreach ($validation_errors as $error) {
                    $validation_errors_string .= '- ' . $error . " \n";
                }

                Flash::danger($validation_errors_string);
                return false;
            }

            $_POST['user_id'] = $_SESSION['user']['id'];

            $city = $this->citiesModel->getById($_POST['city_id']);

            if(!isset($city)){
                Flash::danger('Ville inconnu');
                return false;
            }

            $id = $this->articlesModel->save($_POST);
            $pictureName = $this->uploadUtility->uploadFile($_FILES['picture'], $id);

            $this->articlesModel->attachPicture($id, $pictureName);
            header('Location: /product/' . $id);
            return true;
        } catch (\Exception $e){
            Flash::danger('Une erreur est survenue');
            return false;
        }

    }
    /**
     * Affiche la page d'un produit
     * @return void
     */
    public function showAction(): void
    {
        $id = $this->route_params['id'];

        try {
            Articles::addOneView($id);
            $suggestions = Articles::getSuggest();
            $article = Articles::getOne($id);
        } catch(\Exception $e){
            var_dump($e);
        }

        View::renderTemplate('Product/Show.html', [
            'article' => $article,
            'suggestions' => $suggestions,
            'product_id' => $id,
        ]);
    }
}
