<?php

namespace R301\View;

use InvalidArgumentException;
use PDOException;
use R301\Controleur\ParticipationControleur;
use function R301\Utils\Http_response\deliver_response;

class ParticipationRouteHandler {
    private readonly ParticipationControleur $participations;

    public function __construct(ParticipationControleur $participations)
    {
        $this->participations = $participations;
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
            $data = $this->participations->listerToutesLesParticipations();
            if ($data) {
                deliver_response(200, 'Liste de toutes les participations récuperée avec succèes', $data);
            } else {
                deliver_response(200, 'La base de données ne contient aucun participation enregistré.');
            }
        } catch (PDOException $e) {
            deliver_response(500, 'Erreur lors de la récuperation des participations.');
        }
    }

    public function create(): void
    {
        $data = $this->requestBodyAsArray();

        try {
            $participation = $this->participations->buildParticipationFromArray($data);
            if ($this->participations->assignerUnParticipantByArray($participation)) {
                deliver_response(201, 'Données crée avec succés.');
            } else {
                deliver_response(400, "Problem d'insertion");
            }
        } catch (PDOException $e) {
            deliver_response(500, "Erreur lors de l'insertion du joueur");
        }
    }

    public function getFeuilleDeMatch(array $params): void
    {
        $id = (int)($params['id'] ?? 0);

        try {
            $data = $this->participations->getFeuilleDeMatch($id);
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
        $data = $this->requestBodyAsArray();

        try {
            $participationAModifier = $this->participations->buildParticipationFromArray($data);
            $res = $this->participations->modifierParticipationByArray($participationAModifier);

            if ($res) {
                deliver_response(200, 'Données du participation modifié avec succées.');
            } else {
                $id = (int)($params['id'] ?? 0);
                deliver_response(404, "Participation d'id $id n'existe pas");
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
        $data = $this->participations->supprimerLaParticipation($id);

        if ($data === false) {
            deliver_response(404, "Performance d'id $id n'existe pas");
            return;
        }

        deliver_response(200, "Performance d'id $id supprimée avec succèes");
    }
}
