<?php

namespace R301\View;

use InvalidArgumentException;
use PDOException;
use R301\Controleur\CommentaireControleur;
use function R301\Utils\Http_response\send_error;
use function R301\Utils\Http_response\send_success;

class CommentaireRouteHandler {
    private readonly CommentaireControleur $commentaires;

    public function __construct(CommentaireControleur $commentaires)
    {
        $this->commentaires = $commentaires;
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

    private function readIds(array $params): array
    {
        $joueurId = (int)($params['id'] ?? 0);
        $commentaireId = (int)($params['commentaireId'] ?? 0);

        return [$joueurId, $commentaireId];
    }

    public function list(array $params): void
    {
        $joueurId = (int)($params['id'] ?? 0);
        if ($joueurId <= 0) {
            send_error(400, 'Identifiant joueur invalide.', 'INVALID_PLAYER_ID');
            return;
        }

        try {
            $commentaires = $this->commentaires->listerLesCommentairesDuJoueur($joueurId);
            send_success(200, 'Commentaires récupérés.', $commentaires, ['count' => count($commentaires)]);
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la récupération des commentaires.', 'DATABASE_ERROR');
        }
    }

    public function create(array $params): void
    {
        $joueurId = (int)($params['id'] ?? 0);
        if ($joueurId <= 0) {
            send_error(400, 'Identifiant joueur invalide.', 'INVALID_PLAYER_ID');
            return;
        }

        $data = $this->requestBodyAsArray();
        if ($data === null) {
            send_error(400, 'Corps JSON invalide.', 'INVALID_JSON');
            return;
        }

        $contenu = trim((string)($data['contenu'] ?? ''));
        if ($contenu === '') {
            send_error(422, 'Contenu du commentaire invalide.', 'VALIDATION_ERROR');
            return;
        }

        try {
            $created = $this->commentaires->ajouterCommentaire($contenu, $joueurId);
            if (!$created) {
                send_error(404, 'Joueur introuvable.', 'PLAYER_NOT_FOUND');
                return;
            }

            send_success(201, 'Commentaire créé.', []);
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la création du commentaire.', 'DATABASE_ERROR');
        } catch (InvalidArgumentException $e) {
            send_error(422, 'Données de commentaire invalides.', 'VALIDATION_ERROR', $e->getMessage());
        }
    }

    public function update(array $params): void
    {
        [$joueurId, $commentaireId] = $this->readIds($params);
        if ($joueurId <= 0 || $commentaireId <= 0) {
            send_error(400, 'Identifiants invalides.', 'INVALID_ID');
            return;
        }

        $data = $this->requestBodyAsArray();
        if ($data === null) {
            send_error(400, 'Corps JSON invalide.', 'INVALID_JSON');
            return;
        }

        $contenu = trim((string)($data['contenu'] ?? ''));
        if ($contenu === '') {
            send_error(422, 'Contenu du commentaire invalide.', 'VALIDATION_ERROR');
            return;
        }

        try {
            $updated = $this->commentaires->modifierCommentaire($joueurId, $commentaireId, $contenu);
            if (!$updated) {
                send_error(404, 'Commentaire introuvable pour ce joueur.', 'COMMENT_NOT_FOUND');
                return;
            }

            send_success(200, 'Commentaire mis à jour.', []);
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la mise à jour du commentaire.', 'DATABASE_ERROR');
        }
    }

    public function delete(array $params): void
    {
        [$joueurId, $commentaireId] = $this->readIds($params);
        if ($joueurId <= 0 || $commentaireId <= 0) {
            send_error(400, 'Identifiants invalides.', 'INVALID_ID');
            return;
        }

        try {
            $deleted = $this->commentaires->supprimerCommentaire($joueurId, $commentaireId);
            if (!$deleted) {
                send_error(404, 'Commentaire introuvable pour ce joueur.', 'COMMENT_NOT_FOUND');
                return;
            }

            send_success(200, 'Commentaire supprimé.', []);
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la suppression du commentaire.', 'DATABASE_ERROR');
        }
    }
}
