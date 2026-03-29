<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/Psr4AutoloaderClass.php';
require_once 'Utils/Http_Response.php';
require_once 'Utils/Jwt_Util.php';

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
use function R301\Utils\Jwt\get_bearer_token;
use function R301\Utils\Jwt\verify_token_with_auth_api;
use function R301\Utils\Http_response\deliver_response;

const AUTH_VERIFY_URL = 'https://r401auth.alwaysdata.net/auth/verify';

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

$withAuth = function (callable $handler): callable {
    return function (array $params = []) use ($handler): void {
        $token = get_bearer_token();
        if ($token === null) {
            deliver_response(401, 'Token manquant');
            return;
        }

        if (!verify_token_with_auth_api($token, AUTH_VERIFY_URL)) {
            deliver_response(401, 'Token invalide ou expiré');
            return;
        }

        if ($params === []) {
            $handler();
            return;
        }

        $handler($params);
    };
};

$joueurByIdRoute = '/joueurs/{id}';
$rencontreByIdRoute = '/rencontre/{id}';
$feuilleDeMatcheByIdRoute = '/feuilledematche/{id}';

$router->get('/joueurs', $withAuth([$joueurRouteHandler, 'list']));
$router->post('/joueurs', $withAuth([$joueurRouteHandler, 'create']));
$router->get($joueurByIdRoute, $withAuth([$joueurRouteHandler, 'get']));
$router->put($joueurByIdRoute, $withAuth([$joueurRouteHandler, 'update']));
$router->delete($joueurByIdRoute, $withAuth([$joueurRouteHandler, 'delete']));

$router->get('/rencontre', $withAuth([$rencontreRouteHandler, 'list']));
$router->post('/rencontre', $withAuth([$rencontreRouteHandler, 'create']));
$router->get($rencontreByIdRoute, $withAuth([$rencontreRouteHandler, 'get']));
$router->put($rencontreByIdRoute, $withAuth([$rencontreRouteHandler, 'update']));
$router->delete($rencontreByIdRoute, $withAuth([$rencontreRouteHandler, 'delete']));

$router->get('/feuilledematche', $withAuth([$participationRouteHandler, 'list']));
$router->post('/feuilledematche', $withAuth([$participationRouteHandler, 'create']));
$router->get($feuilleDeMatcheByIdRoute, $withAuth([$participationRouteHandler, 'getFeuilleDeMatch']));
$router->put($feuilleDeMatcheByIdRoute, $withAuth([$participationRouteHandler, 'update']));
$router->delete($feuilleDeMatcheByIdRoute, $withAuth([$participationRouteHandler, 'delete']));

$router->get('/statistiques', $withAuth([$statistiquesRouteHandler, 'getEquipe']));
$router->get('/statistiques/joueurs', $withAuth([$statistiquesRouteHandler, 'getJoueurs']));
$router->get('/statistiques/joueurs/{id}', $withAuth([$statistiquesRouteHandler, 'getJoueur']));

if (!$router->dispatch($httpMethod, $resource)) {
    deliver_response(404, 'Route non trouvée');
}
