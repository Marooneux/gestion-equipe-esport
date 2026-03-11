<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Psr4AutoloaderClass.php';
require_once 'Utils/Http_Response.php';
use R301\Psr4AutoloaderClass;
use function R301\Utils\Http_response\deliver_response;
use R301\Controleur\JoueurControleur;

$loader = new Psr4AutoloaderClass;
// register the autoloader
$loader->register();
// register the base directories for the namespace prefix
$loader->addNamespace('R301', '.');

$http_method = $_SERVER["REQUEST_METHOD"];
$uri = strtok($_SERVER["REQUEST_URI"], '?');

switch($http_method) {
    case 'GET':
        $joueurs = JoueurControleur::getInstance();
        $data = $joueurs->listerTousLesJoueurs();
        print_r($data);
        deliver_response(200, "Donn[ee récuperée avec succèes", $data);
        break;
}

?>