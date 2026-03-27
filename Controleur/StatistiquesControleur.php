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

    // TODO: implémenter quand l'endpoint backend sera disponible
    public function getStatistiquesEquipe(): array {
        return api_get('/statistiques/equipe')['data'] ?? [];
    }

    // TODO: implémenter quand l'endpoint backend sera disponible
    public function getStatistiquesJoueurs(): array {
        return api_get('/statistiques/joueurs')['data'] ?? [];
    }
}
