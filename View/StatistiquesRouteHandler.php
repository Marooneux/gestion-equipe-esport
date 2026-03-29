<?php

namespace R301\View;

use PDOException;
use R301\Controleur\StatistiquesControleur;
use function R301\Utils\Http_response\deliver_response;

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
            if ($data) {
                deliver_response(200, 'Stats récuperée avec succèes', $data);
            } else {
                deliver_response(200, 'La base de données ne contient aucun rencontre.');
            }
        } catch (PDOException $e) {
            deliver_response(500, 'Erreur lors de la récuperation des joueurs.');
        }
    }

    public function getJoueurs(): void
    {
        try {
            $data = $this->statistiques->getStatistiquesTousLesJoueurs();
            if ($data) {
                deliver_response(200, 'Stats des joueurs récuperées avec succèes', $data);
            } else {
                deliver_response(200, 'Aucun joueur trouvé.');
            }
        } catch (PDOException $e) {
            deliver_response(500, 'Erreur lors de la récupération des stats joueurs.');
        }
    }

    public function getJoueur(array $params): void
    {
        $id = (int)($params['id'] ?? 0);

        try {
            $data = $this->statistiques->getStatistiquesDUnJoueur($id);
            if ($data === false) {
                deliver_response(404, "Joueur d'id $id n'existe pas");
            } else {
                deliver_response(200, 'Stats du joueur récupérées avec succès', $data);
            }
        } catch (PDOException $e) {
            deliver_response(500, 'Erreur lors de la récupération des stats du joueur.');
        }
    }
}
