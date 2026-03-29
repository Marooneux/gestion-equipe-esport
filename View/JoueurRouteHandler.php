<?php

namespace R301\View;

use InvalidArgumentException;
use PDOException;
use R301\Controleur\CommentaireControleur;
use R301\Controleur\JoueurControleur;
use R301\Modele\Joueur\Joueur;
use function R301\Utils\Http_response\send_error;
use function R301\Utils\Http_response\send_success;

class JoueurRouteHandler {
    private readonly JoueurControleur $joueurs;
    private readonly CommentaireControleur $commentaires;

    public function __construct(JoueurControleur $joueurs, CommentaireControleur $commentaires)
    {
        $this->joueurs = $joueurs;
        $this->commentaires = $commentaires;
    }

    private function joueurToResponse(Joueur $joueur): array
    {
        $response = $joueur->jsonSerialize();
        $response['commentaires'] = $this->commentaires->listerLesCommentairesDuJoueur($joueur->getJoueurId());

        return $response;
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
            $data = $this->joueurs->listerTousLesJoueurs();
            $joueurs = array_map(
                function (Joueur $joueur): array {
                    return $this->joueurToResponse($joueur);
                },
                is_array($data) ? $data : []
            );
            send_success(200, 'Joueurs récupérés.', $joueurs, ['count' => count($joueurs)]);
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la récupération des joueurs.', 'DATABASE_ERROR');
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
            $joueur = Joueur::buildJoueurFromArray($data);
            $this->joueurs->ajouterJoueurFromArray($joueur);
            send_success(201, 'Joueur créé.', []);
        } catch (PDOException $e) {
            send_error(500, "Erreur lors de la création du joueur.", 'DATABASE_ERROR');
        } catch (InvalidArgumentException $e) {
            send_error(422, 'Données de joueur invalides.', 'VALIDATION_ERROR', $e->getMessage());
        }
    }

    public function get(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            send_error(400, 'Identifiant invalide.', 'INVALID_ID');
            return;
        }

        $data = $this->joueurs->getJoueurById($id);

        if ($data === false) {
            send_error(404, 'Joueur introuvable.', 'PLAYER_NOT_FOUND');
            return;
        }

        send_success(200, 'Joueur récupéré.', $this->joueurToResponse($data));
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
            $joueurAModifier = Joueur::buildJoueurFromArray($data);
            $joueurAModifier->setJoueurId($id);
            $res = $this->joueurs->modifierJoueurByArray($joueurAModifier);

            if ($res) {
                send_success(200, 'Joueur mis à jour.', []);
            } else {
                send_error(404, 'Joueur introuvable.', 'PLAYER_NOT_FOUND');
            }
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la mise à jour du joueur.', 'DATABASE_ERROR');
        } catch (InvalidArgumentException $e) {
            send_error(422, 'Données de joueur invalides.', 'VALIDATION_ERROR', $e->getMessage());
        }
    }

    public function delete(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            send_error(400, 'Identifiant invalide.', 'INVALID_ID');
            return;
        }

        $data = $this->joueurs->supprimerJoueur($id);

        if ($data === false) {
            send_error(404, 'Joueur introuvable.', 'PLAYER_NOT_FOUND');
            return;
        }

        send_success(200, 'Joueur supprimé.', []);
    }
}
