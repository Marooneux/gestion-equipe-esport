<?php

namespace R301\Controleur;

require_once __DIR__ . '/ApiClient.php';

class StatistiquesControleur {
    private static ?StatistiquesControleur $instance = null;

    private function __construct() {}

    public static function getInstance(): StatistiquesControleur {
        if (self::$instance === null) {
            self::$instance = new StatistiquesControleur();
        }
        return self::$instance;
    }

    public function getStatistiquesEquipe(): array {
        $data = api_get('/statistiques')['data'] ?? [];
        return [
            'nbVictoires'          => $data['victoires'] ?? 0,
            'nbNuls'               => $data['nuls'] ?? 0,
            'nbDefaites'           => $data['defaites'] ?? 0,
            'pourcentageVictoires' => $data['pourcentage_victoires'] ?? 0,
            'pourcentageNuls'      => $data['pourcentage_nuls'] ?? 0,
            'pourcentageDefaites'  => $data['pourcentage_defaites'] ?? 0,
        ];
    }

    public function getStatistiquesJoueurs(): array {
        $data = api_get('/statistiques/joueurs')['data'] ?? [];
        return array_map(fn($s) => [
            'joueur_id'             => $s['joueur_id'],
            'posteLePlusPerformant' => $s['poste_le_plus_performant'],
            'nbConsecutifs'         => $s['nb_rencontres_consecutives'],
            'nbTitularisations'     => $s['nb_titularisations'],
            'nbRemplacants'         => $s['nb_remplacant'],
            'moyenneEvaluations'    => $s['moyenne_evaluations'],
            'pourcentageGagnes'     => $s['pourcentage_matchs_gagnes'],
        ], $data);
    }
}
