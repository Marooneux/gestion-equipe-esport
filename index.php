<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/Psr4AutoloaderClass.php';
require_once 'Utils/Http_Response.php';

use R301\Psr4AutoloaderClass;
use R301\Controleur\JoueurControleur;
use R301\Controleur\ParticipationControleur;
use R301\Controleur\RencontreControleur;
use R301\Controleur\StatistiquesControleur;
use R301\View\JoueurRouteHandler;
use R301\View\ParticipationRouteHandler;
use R301\View\RencontreRouteHandler;
use R301\View\StatistiquesRouteHandler;
use R301\Utils\Routing\Router;
use function R301\Utils\Http_response\deliver_response;

$loader = new Psr4AutoloaderClass();
$loader->register();
$loader->addNamespace('R301', '.');

$httpMethod = $_SERVER['REQUEST_METHOD'];
$resource = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$resource = rtrim($resource, '/');
if ($resource === '') {
    $resource = '/';
}

$joueursController = JoueurControleur::getInstance();
$rencontresController = RencontreControleur::getInstance();
$participationsController = ParticipationControleur::getInstance();
$statistiquesController = StatistiquesControleur::getInstance();

$joueurRouteHandler = new JoueurRouteHandler($joueursController);
$rencontreRouteHandler = new RencontreRouteHandler($rencontresController);
$participationRouteHandler = new ParticipationRouteHandler($participationsController);
$statistiquesRouteHandler = new StatistiquesRouteHandler($statistiquesController);

$router = new Router();

$joueurByIdRoute = '/joueurs/{id}';
$rencontreByIdRoute = '/rencontre/{id}';
$feuilleDeMatcheByIdRoute = '/feuilledematche/{id}';

$router->get('/joueurs', [$joueurRouteHandler, 'list']);
$router->post('/joueurs', [$joueurRouteHandler, 'create']);
$router->get($joueurByIdRoute, [$joueurRouteHandler, 'get']);
$router->put($joueurByIdRoute, [$joueurRouteHandler, 'update']);
$router->delete($joueurByIdRoute, [$joueurRouteHandler, 'delete']);

$router->get('/rencontre', [$rencontreRouteHandler, 'list']);
$router->post('/rencontre', [$rencontreRouteHandler, 'create']);
$router->get($rencontreByIdRoute, [$rencontreRouteHandler, 'get']);
$router->put($rencontreByIdRoute, [$rencontreRouteHandler, 'update']);
$router->delete($rencontreByIdRoute, [$rencontreRouteHandler, 'delete']);

$router->get('/feuilledematche', [$participationRouteHandler, 'list']);
$router->post('/feuilledematche', [$participationRouteHandler, 'create']);
$router->get($feuilleDeMatcheByIdRoute, [$participationRouteHandler, 'getFeuilleDeMatch']);
$router->put($feuilleDeMatcheByIdRoute, [$participationRouteHandler, 'update']);
$router->delete($feuilleDeMatcheByIdRoute, [$participationRouteHandler, 'delete']);

$router->get('/statistiques', [$statistiquesRouteHandler, 'getEquipe']);
$router->get('/statistiques/joueurs', [$statistiquesRouteHandler, 'getJoueurs']);
$router->get('/statistiques/joueurs/{id}', [$statistiquesRouteHandler, 'getJoueur']);

if (!$router->dispatch($httpMethod, $resource)) {
    deliver_response(404, 'Route non trouvée');
}
