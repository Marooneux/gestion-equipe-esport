<?php

namespace R301\View;

use InvalidArgumentException;
use PDOException;
use R301\Controleur\RencontreControleur;
use R301\Modele\Rencontre\Rencontre;
use function R301\Utils\Http_response\send_error;
use function R301\Utils\Http_response\send_success;

class RencontreRouteHandler {
    private readonly RencontreControleur $rencontres;

    public function __construct(RencontreControleur $rencontres)
    {
        $this->rencontres = $rencontres;
    }

    private function requestBodyAsArray(): ?array
    {
        $body = file_get_contents('php://input');
        if ($body === false || trim($body) === '') {
            return [];
        }

        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return null;
        }

        return $data;
    }

    public function list(): void
    {
        try {
            $data = $this->rencontres->listerToutesLesRencontres();
            $rencontres = is_array($data) ? $data : [];
            send_success(200, 'Rencontres récupérées.', $rencontres, ['count' => count($rencontres)]);
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la récupération des rencontres.', 'DATABASE_ERROR');
        }
    }

    public function create(): void
    {
        $data = $this->requestBodyAsArray();
        if ($data === null) {
            send_error(400, 'Corps JSON invalide.', 'INVALID_JSON');
            return;
        }

        try {
            $rencontre = Rencontre::buildRencontreFromArray($data);
            $this->rencontres->ajouterRencontreFromArray($rencontre);
            send_success(201, 'Rencontre créée.', []);
        } catch (PDOException $e) {
            send_error(500, "Erreur lors de la création de la rencontre.", 'DATABASE_ERROR');
        } catch (InvalidArgumentException $e) {
            send_error(422, 'Données de rencontre invalides.', 'VALIDATION_ERROR', $e->getMessage());
        }
    }

    public function get(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            send_error(400, 'Identifiant invalide.', 'INVALID_ID');
            return;
        }

        try {
            $data = $this->rencontres->getRencontreById($id);
            if ($data === false) {
                send_error(404, 'Rencontre introuvable.', 'MATCH_NOT_FOUND');
            } else {
                send_success(200, 'Rencontre récupérée.', $data);
            }
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la récupération de la rencontre.', 'DATABASE_ERROR');
        }
    }

    public function update(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            send_error(400, 'Identifiant invalide.', 'INVALID_ID');
            return;
        }

        $data = $this->requestBodyAsArray();
        if ($data === null) {
            send_error(400, 'Corps JSON invalide.', 'INVALID_JSON');
            return;
        }

        try {
            $rencontreAModifier = Rencontre::buildRencontreFromArray($data);
            if ($rencontreAModifier === false) {
                send_error(422, 'Données de rencontre invalides.', 'VALIDATION_ERROR');
                return;
            }
            $res = $this->rencontres->modifierRencontreByArray($rencontreAModifier);

            if ($res) {
                send_success(200, 'Rencontre mise à jour.', []);
            } else {
                send_error(404, 'Rencontre introuvable.', 'MATCH_NOT_FOUND');
            }
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la mise à jour de la rencontre.', 'DATABASE_ERROR');
        } catch (InvalidArgumentException $e) {
            send_error(422, 'Données de rencontre invalides.', 'VALIDATION_ERROR', $e->getMessage());
        }
    }

    public function setResultat(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            send_error(400, 'Identifiant invalide.', 'INVALID_ID');
            return;
        }

        $data = $this->requestBodyAsArray();
        if ($data === null || !isset($data['resultat'])) {
            send_error(400, 'Champ resultat manquant.', 'INVALID_BODY');
            return;
        }

        try {
            $res = $this->rencontres->enregistrerResultat($id, $data['resultat']);
            if ($res) {
                send_success(200, 'Résultat enregistré.', []);
            } else {
                send_error(422, 'Impossible d\'enregistrer le résultat (match non passé ou introuvable).', 'VALIDATION_ERROR');
            }
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de l\'enregistrement du résultat.', 'DATABASE_ERROR');
        }
    }

    public function delete(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            send_error(400, 'Identifiant invalide.', 'INVALID_ID');
            return;
        }

        try {
            $data = $this->rencontres->supprimerRencontre($id);
            if ($data === false) {
                send_error(404, 'Rencontre introuvable.', 'MATCH_NOT_FOUND');
            } else {
                send_success(200, 'Rencontre supprimée.', []);
            }
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la suppression de la rencontre.', 'DATABASE_ERROR');
        }
    }
}
