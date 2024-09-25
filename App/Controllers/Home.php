<?php

namespace App\Controllers;

use App\Models\Articles;
use \Core\View;
use DateTime;
use Exception;

/**
 * Home controller
 */
class Home extends \Core\Controller
{

    /**
     * Affiche la page d'accueil
     *
     * @return void
     * @throws \Exception
     */
    public function indexAction()
    {
        try{
            self::addTodayVisit();

        }catch (Exception $e){
            var_dump($e);
        }
        View::renderTemplate('Home/index.html', []);
    }

    public static function addTodayVisit(){
        $shouldCount = true;
        $today_date = new DateTime();

        try {
            if(isset($_SESSION['today_visit'])) {
                $last_visit_date = new DateTime($_SESSION['today_visit']);
                $diff = $today_date->diff($last_visit_date);

                if ($diff->d == 0){
                    $shouldCount = false;
                }
            }
        } catch (Exception $e) {
            $shouldCount = false;
        }

        $_SESSION['today_visit'] = $today_date->format('Y-m-d');

        if($shouldCount){
            \App\Models\User::addTodaysUser();
        }
    }
}
