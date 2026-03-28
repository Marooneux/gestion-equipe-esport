<?php
namespace R301\Controleur;

require_once __DIR__ . '/ApiClient.php';

class ParticipationControleur {
    private static ?ParticipationControleur $instance = null;

    private function __construct() {}

    public static function getInstance(): ParticipationControleur {
        if (self::$instance === null) {
            self::$instance = new ParticipationControleur();
        }
        return self::$instance;
    }

    public function listerToutesLesParticipations(): array {
        $reponse = api_get('/feuilledematche');
        return $reponse['data'] ?? [];
    }

    public function getFeuilleDeMatch(int $rencontreId): array {
        $toutes = $this->listerToutesLesParticipations();
        return array_values(array_filter($toutes, fn($p) => $p['rencontre']['id'] === $rencontreId));
    }

    public function getParticipantAuPoste(array $feuille, string $poste, string $titulaireOuRemplacant): ?array {
        foreach ($feuille as $participation) {
            if ($participation['poste'] === $poste && $participation['titularité'] === $titulaireOuRemplacant) {
                return $participation;
            }
        }
        return null;
    }

    public function feuilleEstComplete(array $feuille): bool {
        $postes = ['TOPLANE', 'JUNGLE', 'MIDLANE', 'ADCARRY', 'SUPPORT'];
        foreach ($postes as $poste) {
            if ($this->getParticipantAuPoste($feuille, $poste, 'TITULAIRE') === null) {
                return false;
            }
        }
        foreach ($feuille as $participation) {
            if ($participation['joueur']['statut'] !== 'ACTIF') {
                return false;
            }
        }
        return true;
    }

    public function feuilleEstEvaluee(array $feuille): bool {
        foreach ($feuille as $participation) {
            if ($participation['performance'] === null) {
                return false;
            }
        }
        return true;
    }

    public function assignerUnParticipant(
        int $joueurId,
        int $rencontreId,
        string $poste,
        string $titulaireOuRemplacant
    ): bool {
        $donnees = [
            'joueur_id'    => $joueurId,
            'rencontre_id' => $rencontreId,
            'poste'        => $poste,
            'titularité'   => $titulaireOuRemplacant,
        ];
        $reponse = api_post('/feuilledematche', $donnees);
        return isset($reponse['status_code']) && $reponse['status_code'] === 201;
    }

    private function getParticipationById(int $participationId): ?array {
        foreach ($this->listerToutesLesParticipations() as $p) {
            if ($p['id'] === $participationId) return $p;
        }
        return null;
    }

    public function modifierParticipation(int $participationId, string $poste, string $titulaireOuRemplacant, int $joueurId): bool {
        $p = $this->getParticipationById($participationId);
        if ($p === null) return false;
        $donnees = [
            'id'           => $participationId,
            'joueur_id'    => $joueurId,
            'rencontre_id' => $p['rencontre']['id'],
            'poste'        => $poste,
            'titularité'   => $titulaireOuRemplacant,
        ];
        $reponse = api_put("/feuilledematche/$participationId", $donnees);
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }

    public function supprimerLaParticipation(int $participationId): bool {
        $reponse = api_delete("/feuilledematche/$participationId");
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }

    public function mettreAJourLaPerformance(int $participationId, string $performance): bool {
        $p = $this->getParticipationById($participationId);
        if ($p === null) return false;
        $donnees = [
            'id'           => $participationId,
            'joueur_id'    => $p['joueur']['id'],
            'rencontre_id' => $p['rencontre']['id'],
            'poste'        => $p['poste'],
            'titularité'   => $p['titularité'],
            'performance'  => $performance,
        ];
        $reponse = api_put("/feuilledematche/$participationId", $donnees);
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }

    public function supprimerLaPerformance(int $participationId): bool {
        $p = $this->getParticipationById($participationId);
        if ($p === null) return false;
        $donnees = [
            'id'           => $participationId,
            'joueur_id'    => $p['joueur']['id'],
            'rencontre_id' => $p['rencontre']['id'],
            'poste'        => $p['poste'],
            'titularité'   => $p['titularité'],
        ];
        $reponse = api_put("/feuilledematche/$participationId", $donnees);
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }
}
