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

if($resource == "/joueurs") {
    switch($http_method) {
        case 'GET':
            $joueurs = JoueurControleur::getInstance();
            $data = $joueurs->listerTousLesJoueurs();
            deliver_response(200, "Donn[ee récuperée avec succèes", $data);
            break;
        case 'POST':
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
            $joueur = Joueur::buildFromArray($data);
            $joueursClass = JoueurControleur::getInstance();
            if($joueursClass->ajouterJoueurFromArray($joueur)) {
                deliver_response("201", "Données crée avec succés.", $data);
            }
    }

}

?>