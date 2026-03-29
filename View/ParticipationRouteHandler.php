<?php

namespace R301\View;

use InvalidArgumentException;
use PDOException;
use R301\Controleur\ParticipationControleur;
use function R301\Utils\Http_response\send_error;
use function R301\Utils\Http_response\send_success;

class ParticipationRouteHandler {
    private readonly ParticipationControleur $participations;

    public function __construct(ParticipationControleur $participations)
    {
        $this->participations = $participations;
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
            $data = $this->participations->listerToutesLesParticipations();
            $participations = is_array($data) ? $data : [];
            send_success(200, 'Participations récupérées.', $participations, ['count' => count($participations)]);
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la récupération des participations.', 'DATABASE_ERROR');
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
            $participation = $this->participations->buildParticipationFromArray($data);
            if ($this->participations->assignerUnParticipantByArray($participation)) {
                send_success(201, 'Participation créée.', []);
            } else {
                send_error(409, "Impossible de créer la participation.", 'PARTICIPATION_CONFLICT');
            }
        } catch (PDOException $e) {
            send_error(500, "Erreur lors de la création de la participation.", 'DATABASE_ERROR');
        } catch (InvalidArgumentException $e) {
            send_error(422, 'Données de participation invalides.', 'VALIDATION_ERROR', $e->getMessage());
        }
    }

    public function getFeuilleDeMatch(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            send_error(400, 'Identifiant invalide.', 'INVALID_ID');
            return;
        }

        try {
            $data = $this->participations->getFeuilleDeMatch($id);
            if ($data === false) {
                send_error(404, 'Feuille de match introuvable.', 'MATCH_SHEET_NOT_FOUND');
            } else {
                send_success(200, 'Feuille de match récupérée.', $data);
            }
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la récupération de la feuille de match.', 'DATABASE_ERROR');
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
            $participationAModifier = $this->participations->buildParticipationFromArray($data);
            if (method_exists($participationAModifier, 'setParticipationId')) {
                $participationAModifier->setParticipationId($id);
            }
            $res = $this->participations->modifierParticipationByArray($participationAModifier);

            if ($res) {
                send_success(200, 'Participation mise à jour.', []);
            } else {
                send_error(404, 'Participation introuvable.', 'PARTICIPATION_NOT_FOUND');
            }
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la mise à jour de la participation.', 'DATABASE_ERROR');
        } catch (InvalidArgumentException $e) {
            send_error(422, 'Données de participation invalides.', 'VALIDATION_ERROR', $e->getMessage());
        }
    }

    public function delete(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            send_error(400, 'Identifiant invalide.', 'INVALID_ID');
            return;
        }

        $data = $this->participations->supprimerLaParticipation($id);

        if ($data === false) {
            send_error(404, 'Participation introuvable.', 'PARTICIPATION_NOT_FOUND');
            return;
        }

        send_success(200, 'Participation supprimée.', []);
    }
}
