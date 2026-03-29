<?php

namespace R301\View;

use PDOException;
use R301\Controleur\StatistiquesControleur;
use function R301\Utils\Http_response\send_error;
use function R301\Utils\Http_response\send_success;

class StatistiquesRouteHandler {
    private readonly StatistiquesControleur $statistiques;

    public function __construct(StatistiquesControleur $statistiques)
    {
        $this->statistiques = $statistiques;
    }

    public function getEquipe(): void
    {
        try {
            $data = $this->statistiques->getStatistiquesEquipe();
            $stats = is_array($data) ? $data : [];
            send_success(200, 'Statistiques équipe récupérées.', $stats);
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la récupération des statistiques équipe.', 'DATABASE_ERROR');
        }
    }

    public function getJoueurs(): void
    {
        try {
            $data = $this->statistiques->getStatistiquesTousLesJoueurs();
            $stats = is_array($data) ? $data : [];
            send_success(200, 'Statistiques joueurs récupérées.', $stats, ['count' => count($stats)]);
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la récupération des statistiques joueurs.', 'DATABASE_ERROR');
        }
    }

    public function getJoueur(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            send_error(400, 'Identifiant invalide.', 'INVALID_ID');
            return;
        }

        try {
            $data = $this->statistiques->getStatistiquesDUnJoueur($id);
            if ($data === false) {
                send_error(404, 'Joueur introuvable.', 'PLAYER_NOT_FOUND');
            } else {
                send_success(200, 'Statistiques du joueur récupérées.', $data);
            }
        } catch (PDOException $e) {
            send_error(500, 'Erreur lors de la récupération des statistiques du joueur.', 'DATABASE_ERROR');
        }
    }
}
