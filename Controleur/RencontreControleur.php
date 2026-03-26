<?php

namespace R301\Controleur;

require_once __DIR__ . '/ApiClient.php';

class RencontreControleur {
    private static ?RencontreControleur $instance = null;

    private function __construct() {}

    public static function getInstance(): RencontreControleur {
        if (self::$instance === null) {
            self::$instance = new RencontreControleur();
        }
        return self::$instance;
    }

    public function listerToutesLesRencontres(): array {
        $reponse = api_get('/rencontre');
        return $reponse['data'] ?? [];
    }

    public function getRencontreById(int $id): ?array {
        $reponse = api_get('/rencontre/' . $id);
        return $reponse['data'] ?? null;
    }

    public function rencontreEstPassee(array $rencontre): bool {
        $dateStr = is_array($rencontre['date_heure'])
            ? $rencontre['date_heure']['date']
            : $rencontre['date_heure'];
        return strtotime($dateStr) < time();
    }

    public function ajouterRencontre(
        string $dateHeure,
        string $equipeAdverse,
        string $adresse,
        string $lieu
    ): bool {
        if (strtotime($dateHeure) < time()) {
            return false;
        }
        $donnees = [
            'id' => 0,
            'date_heure' => [
                'date' => date('Y-m-d H:i:s.000000', strtotime($dateHeure)),
                'timezone_type' => 3,
                'timezone' => 'UTC',
            ],
            'equipe_adverse' => $equipeAdverse,
            'adresse' => $adresse,
            'lieu_recontre' => $lieu,
            'resultat' => null,
        ];
        $reponse = api_post('/rencontre', $donnees);
        return isset($reponse['status_code']) && $reponse['status_code'] === 201;
    }

    public function modifierRencontre(
        int $id,
        string $dateHeure,
        string $equipeAdverse,
        string $adresse,
        string $lieu
    ): bool {
        $rencontre = $this->getRencontreById($id);
        if ($rencontre === null || $this->rencontreEstPassee($rencontre) || strtotime($dateHeure) < time()) {
            return false;
        }
        $donnees = [
            'id' => $id,
            'date_heure' => [
                'date' => date('Y-m-d H:i:s.000000', strtotime($dateHeure)),
                'timezone_type' => 3,
                'timezone' => 'UTC',
            ],
            'equipe_adverse' => $equipeAdverse,
            'adresse' => $adresse,
            'lieu_recontre' => $lieu,
            'resultat' => $rencontre['resultat'],
        ];
        $reponse = api_put('/rencontre/' . $id, $donnees);
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }

    public function supprimerRencontre(int $id): bool {
        $rencontre = $this->getRencontreById($id);
        if ($rencontre === null || $rencontre['resultat'] !== null) {
            return false;
        }
        $reponse = api_delete('/rencontre/' . $id);
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }

    // Non disponible : pas d'endpoint dans le backend
    public function enregistrerResultat(int $id, string $resultat): bool {
        return false;
    }
}
