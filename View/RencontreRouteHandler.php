<?php

namespace R301\View;

use InvalidArgumentException;
use PDOException;
use R301\Controleur\RencontreControleur;
use R301\Modele\Rencontre\Rencontre;
use function R301\Utils\Http_response\deliver_response;

class RencontreRouteHandler {
    private readonly RencontreControleur $rencontres;

    public function __construct(RencontreControleur $rencontres)
    {
        $this->rencontres = $rencontres;
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
            $data = $this->rencontres->listerToutesLesRencontres();
            if ($data) {
                deliver_response(200, 'List des rencontres récuperée avec succèes', $data);
            } else {
                deliver_response(200, 'La base de données ne contient aucun rencontre.');
            }
        } catch (PDOException $e) {
            deliver_response(500, 'Erreur lors de la récuperation des joueurs.');
        }
    }

    public function create(): void
    {
        $data = $this->requestBodyAsArray();

        try {
            $rencontre = Rencontre::buildRencontreFromArray($data);
            $this->rencontres->ajouterRencontreFromArray($rencontre);
            deliver_response(201, 'Données crée avec succés.');
        } catch (PDOException $e) {
            deliver_response(500, "Erreur lors de l'insertion du rencontre");
        } catch (InvalidArgumentException $e) {
            deliver_response(400, $e->getMessage());
        }
    }

    public function get(array $params): void
    {
        $id = (int)($params['id'] ?? 0);

        try {
            $data = $this->rencontres->getRencontreById($id);
            if ($data === false) {
                deliver_response(404, "Le joueurs d'id $id n'existe pas");
            } else {
                deliver_response(200, 'Données récuperée avec succèes', $data);
            }
        } catch (PDOException $e) {
            deliver_response(404, "Le joueurs d'id $id n'existe pas");
        }
    }

    public function update(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $data = $this->requestBodyAsArray();

        try {
            $rencontreAModifier = Rencontre::buildRencontreFromArray($data);
            if ($rencontreAModifier === false) {
                deliver_response(400, 'Les données de la rencontre sont invalides.');
                return;
            }
            $res = $this->rencontres->modifierRencontreByArray($rencontreAModifier);

            if ($res) {
                deliver_response(200, 'Données du rencontre modifié avec succées.');
            } else {
                deliver_response(404, "Rencontre d'id $id n'existe pas");
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

        try {
            $data = $this->rencontres->supprimerRencontre($id);
            if ($data === false) {
                deliver_response(404, "Rencontre d'id $id n'existe pas");
            } else {
                deliver_response(200, "Rencontre d'id $id supprimée avec succèes");
            }
        } catch (PDOException $e) {
            deliver_response(404, "Rencontre d'id $id n'existe pas");
        }
    }
}
