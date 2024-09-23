<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Utility\Flash;
use App\Utility\Upload;
use \Core\View;

/**
 * Product controller
 */
class Product extends \Core\Controller
{

    /**
     * Affiche la page d'ajout
     * @return void
     */
    public function indexAction()
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

    public function addProduct(){
        try {
            $validation_errors = [];

            if(empty($_POST['name']) || $_POST['name'] == '' || strlen($_POST['name']) > 200) {
                $validation_errors[] = 'Le nom est requis et ne peut pas dépasser 200 caractères';
            }

            if(empty($_POST['description']) || $_POST['description'] == '') {
                $validation_errors[] = 'La description est requise';
            }

            if(empty($_POST['ville']) || $_POST['ville'] == '') {
                $validation_errors[] = 'La ville est requis';
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

            if(Articles::getVille($_POST['ville']) > 0){
                $id = Articles::save($_POST);
            } else {
                Flash::danger('Ville inconnu');
                return false;
            }

            $pictureName = Upload::uploadFile($_FILES['picture'], $id);

            Articles::attachPicture($id, $pictureName);
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
    public function showAction()
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
            'article' => $article[0],
            'suggestions' => $suggestions,
            'product_id' => $id,
        ]);
    }
}
