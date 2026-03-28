<?php

namespace R301\View;

use InvalidArgumentException;
use PDOException;
use R301\Controleur\JoueurControleur;
use R301\Modele\Joueur\Joueur;
use function R301\Utils\Http_response\deliver_response;

class JoueurRouteHandler {
    private readonly JoueurControleur $joueurs;

    public function __construct(JoueurControleur $joueurs)
    {
        $this->joueurs = $joueurs;
    }

    private function requestBodyAsArray(): array
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }

    public function list(): void
    {
        try {
            $data = $this->joueurs->listerTousLesJoueurs();
            if ($data) {
                deliver_response(200, 'Liste de joueurs récuperée avec succèes', $data);
            } else {
                deliver_response(200, 'La base de données ne contient aucun joueur.');
            }
        } catch (PDOException $e) {
            deliver_response(500, 'Erreur lors de la récuperation des joueurs.');
        }
    }

    public function create(): void
    {
        $data = $this->requestBodyAsArray();

        try {
            $joueur = Joueur::buildJoueurFromArray($data);
            $this->joueurs->ajouterJoueurFromArray($joueur);
            deliver_response(201, 'Données crée avec succés.');
        } catch (PDOException $e) {
            deliver_response(500, "Erreur lors de l'insertion du joueur");
        } catch (InvalidArgumentException $e) {
            deliver_response(400, $e->getMessage());
        }
    }

    public function get(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $data = $this->joueurs->getJoueurById($id);

        if ($data === false) {
            deliver_response(404, "Le joueurs d'id $id n'existe pas");
            return;
        }

        deliver_response(200, 'Données récuperée avec succèes', $data);
    }

    public function update(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $data = $this->requestBodyAsArray();

        try {
            $joueurAModifier = Joueur::buildJoueurFromArray($data);
            $joueurAModifier->setJoueurId($id);
            $res = $this->joueurs->modifierJoueurByArray($joueurAModifier);

            if ($res) {
                deliver_response(200, 'Données du joueur modifié avec succées.');
            } else {
                deliver_response(404, "Joueur d'id $id n'existe pas");
            }
        } catch (PDOException $e) {
            deliver_response(500, 'Erreur pendand la modification de la ressource');
        } catch (InvalidArgumentException $e) {
            deliver_response(400, $e->getMessage());
        }
    }

    public function delete(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $data = $this->joueurs->supprimerJoueur($id);

        if ($data === false) {
            deliver_response(404, "Joueur d'id $id n'existe pas");
            return;
        }

        deliver_response(200, "Joueur d'id $id supprimée avec succèes");
    }
}
