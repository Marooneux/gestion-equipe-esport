<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Psr4AutoloaderClass.php';
require_once 'Utils/Http_Response.php';
use R301\Psr4AutoloaderClass;
use function R301\Utils\Http_response\deliver_response;
use R301\Controleur\JoueurControleur;
use R301\Modele\Joueur\Joueur;
use R301\Controleur\RencontreControleur;
use R301\Modele\Rencontre\Rencontre;
use R301\Controleur\ParticipationControleur;
use R301\Modele\Participation\Participation;

$loader = new Psr4AutoloaderClass;
// register the autoloader
$loader->register();
// register the base directories for the namespace prefix
$loader->addNamespace('R301', '.');

$http_method = $_SERVER["REQUEST_METHOD"];
$resource = strtok($_SERVER["REQUEST_URI"], '?');
$joueursController = JoueurControleur::getInstance();
$rencontresController = RencontreControleur::getInstance();
$participationsController = ParticipationControleur::getInstance();


if(rtrim($resource, "/") == "/joueurs") {
    switch($http_method) {
        case 'GET':
            try {
                $data = $joueursController->listerTousLesJoueurs();
                if($data == true) {
                    deliver_response(200, "Liste de joueurs récuperée avec succèes", $data);
                } else {
                    deliver_response(200, "La base de données ne contient aucun joueur.");
                }
            } catch(PDOException $e) {
                deliver_response(500, "Erreur lors de la récuperation des joueurs.");
            }
            break;
        case 'POST':
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
            $joueur = Joueur::buildJoueurFromArray($data);
            try {
                $joueursController->ajouterJoueurFromArray($joueur);
                deliver_response(201, "Données crée avec succés.");
            } catch(PDOException $e) {
                deliver_response(500, "Erreur lors de l'insertion du joueur");
            }
            break;
            
    }

}

if(preg_match('#^/joueurs/([0-9]+)$#', $resource, $matches) == 1) {
    #Get the id
    $id = $matches[1];

    switch($http_method) {
        case 'GET':
            $data = $joueursController->getJoueurById($id);
            if($data == false) {
                deliver_response(404, "Le joueurs d'id $id n'existe pas");
            } else {
                deliver_response(200, "Données récuperée avec succèes", $data);
            }
            break;
        case 'PUT':
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
            try {
                $joueurAModifier = Joueur::buildJoueurFromArray($data);
                $joueurAModifier->setJoueurId($id);
                $res = $joueursController->modifierJoueurByArray($joueurAModifier);

                if($res) {
                    deliver_response(200, "Données du joueur modifié avec succées.");
                } else {
                    deliver_response(404, "Joueur d'id $id n'existe pas");
                }
            } catch(PDOException $e) {
                deliver_response(500, "Erreur pendand la modification de la ressource");
            } catch(InvalidArgumentException $e) {
                deliver_response(400, $e->getMessage());
            }
            break;
        case 'DELETE':
            $data = $joueursController->supprimerJoueur($id);
            print_r($data);

            if($data == false) {
                deliver_response(404, "Joueur d'id $id n'existe pas");
                } else {
                deliver_response(200, "Joueur d'id $id supprimée avec succèes");
            }
            break;
    }
}

if(rtrim($resource, "/") == '/rencontre') {
    switch($http_method) {
        case 'GET':
            try {
                $data = $rencontresController->listerToutesLesRencontres();
                if($data == true) {
                    deliver_response(200, "List des rencontres récuperée avec succèes", $data);
                } else {
                    deliver_response(200, "La base de données ne contient aucun rencontre.");
                }
            } catch(PDOException $e) {
                deliver_response(500, "Erreur lors de la récuperation des joueurs.");
            }
            break;
        case 'POST':
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
            try {
                $rencontre = Rencontre::buildRencontreFromArray($data);
                if($rencontre == false) { // La date fourni est < à now.

                } 
                $rencontresController->ajouterRencontreFromArray($rencontre);
                deliver_response(201, "Données crée avec succés.");
            } catch(PDOException $e) {
                deliver_response(500, "Erreur lors de l'insertion du rencontre");
            } catch(InvalidArgumentException $e) {
                deliver_response(400, $e->getMessage());
            } 
            break;
    }
}

echo $resource;

if(preg_match('#^/rencontre/([0-9]+)$#', $resource, $matches) == 1) {
    #Get the id
    $id = $matches[1];

    switch($http_method) {
        case 'GET':
            $data = $rencontresController->getRencontreById($id);
            if($data == false) {
                deliver_response(404, "Le joueurs d'id $id n'existe pas");
            } else {
                deliver_response(200, "Données récuperée avec succèes", $data);
            }
            break;
        case 'PUT':
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
            try {
                $renconctreAModifier = Rencontre::buildRencontreFromArray($data);
                $rencontreAModifier->setRencontreId($id);
                $res = $rencontreController->modifierRencontreByArray($rencontreAModifier);

                if($res) {
                    deliver_response(200, "Données du rencontre modifié avec succées.");
                } else {
                    deliver_response(404, "Rencontre d'id $id n'existe pas");
                }
            } catch(PDOException $e) {
                deliver_response(500, "Erreur pendand la modification de la ressource");
            } catch(InvalidArgumentException $e) {
                deliver_response(400, $e->getMessage());
            }
            break;
        case 'DELETE':
            $data = $rencontreController->supprimerRencontre($id);
            print_r($data);

            if($data == false) {
                deliver_response(404, "Rencontre d'id $id n'existe pas");
                } else {
                deliver_response(200, "Rencontre d'id $id supprimée avec succèes");
            }
            break;
    }
}

if(rtrim($resource, "/") == "/feuilledematche") {
    switch($http_method) {
        case 'GET':
            try {
                $data = $participationsController->listerToutesLesParticipations();
                if($data == true) {
                    deliver_response(200, "Liste de toutes les participations récuperée avec succèes", $data);
                } else {
                    deliver_response(200, "La base de données ne contient aucun participation enregistré.");
                }
            } catch(PDOException $e) {
                deliver_response(500, "Erreur lors de la récuperation des participations.");
            }
            break;
        case 'POST':
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
            try {
            $participation = $participationsController->buildParticipationFromArray($data);
                if ($participationsController->assignerUnParticipantByArray($participation)) {
                    deliver_response(201, "Données crée avec succés.");
                } else {
                    deliver_response(400, "Problem d'insertion");
                }
            } catch(PDOException $e) {
                deliver_response(500, "Erreur lors de l'insertion du joueur");
            }
            break;
            
    }
}

if(preg_match('#^/feuilledematche/([0-9]+)$#', $resource, $matches) == 1) {
    #Get the id
    $id = $matches[1];

    switch($http_method) {
        case 'GET':
            $data = $participationsController->getFeuilleDeMatch($id);

            if($data == false) {
                deliver_response(404, "Le joueurs d'id $id n'existe pas");
            } else {
                deliver_response(200, "Données récuperée avec succèes", $data);
            }
            break;
        case 'PUT':
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
            try {
                $participationAModifier = $participationsController->buildParticipationFromArray($data);
                
                $res = $participationsController->modifierParticipationByArray($participationAModifier);

                if($res) {
                    deliver_response(200, "Données du participation modifié avec succées.");
                } else {
                    deliver_response(404, "Participation d'id $id n'existe pas");
                }
            } catch(PDOException $e) {
                deliver_response(500, "Erreur pendand la modification de la ressource");
            } catch(InvalidArgumentException $e) {
                deliver_response(400, $e->getMessage());
            }
            break;
        case 'DELETE':
            $data = $participationsController->supprimerLaParticipation($id);
            print_r($data);

            if($data == false) {
                deliver_response(404, "Performance d'id $id n'existe pas");
                } else {
                deliver_response(200, "Performance d'id $id supprimée avec succèes");
            }
            break;
    }
}

?>