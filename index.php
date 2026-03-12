<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Psr4AutoloaderClass.php';
require_once 'Utils/Http_Response.php';
use R301\Psr4AutoloaderClass;
use function R301\Utils\Http_response\deliver_response;
use R301\Controleur\JoueurControleur;
use R301\Modele\Joueur\Joueur;

$loader = new Psr4AutoloaderClass;
// register the autoloader
$loader->register();
// register the base directories for the namespace prefix
$loader->addNamespace('R301', '.');

$http_method = $_SERVER["REQUEST_METHOD"];
$resource = strtok($_SERVER["REQUEST_URI"], '?');
$joueursController = JoueurControleur::getInstance();

// if($resource == "/joueurs") {
    switch($http_method) {
        case 'GET':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if($id != null && $id == false) {
                // Parametre id fourni mais invalid
                deliver_response(400, "Le parametre id doit être un entier");
            } else {
                if($id == null) {
                    // Parametre id n'a pas été fourni.
                    $data = $joueursController->listerTousLesJoueurs();
                    if($data == false) {
                        deliver_response(200, "La base de données ne contient aucun joueur.");
                        exit();
                    }
                } else {
                    // Parametre id fourni et valid
                    $data = $joueursController->getJoueurById($id);
                    if($data == false) {
                        deliver_response(404, "Le joueurs d'id $id n'existe pas");
                        exit();
                    }
                }

                deliver_response(200, "Données récuperée avec succèes", $data);
            }
            break;
        case 'POST':
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
            $joueur = Joueur::buildFromArray($data);
            if($joueursController->ajouterJoueurFromArray($joueur)) {
                deliver_response("201", "Données crée avec succés.", $data);
            }
            break;
        case 'PUT':
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
            $joueurAModifier = Joueur::buildFromArray($data);
            if($joueursController->modifierJoueurByArray($joueurAModifier)) {
                deliver_response(204, "Données du joueur modifié avec succées.", $data);
            }
            break;
        case 'DELETE':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if($id != null && $id == false) {
                // Parametre id fourni mais invalid
                deliver_response(400, "Le parametre id doit être un entier");
            } else {
                if($id == null) {
                    // Parametre id n'a pas été fourni.
                    deliver_response(400, "Le parametre id doit être un fourni");
                } else {
                    // Parametre id fourni et valid
                    $data = $joueursController->supprimerJoueur($id);
                    print_r($data);

                    if($data == false) {
                        deliver_response(404, "Joueur d'id $id n'existe pas");
                        } else {
                            deliver_response(200, "Joueur d'id $id supprimée avec succèes");
                    }
                }
            }
            break;
            
    // }

}

?>