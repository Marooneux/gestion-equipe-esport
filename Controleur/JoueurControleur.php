<?php

namespace R301\Controleur;

require_once __DIR__ . '/ApiClient.php';

class JoueurControleur {
    private static ?JoueurControleur $instance = null;

    private function __construct() {}

    public static function getInstance(): JoueurControleur {
        if (self::$instance === null) {
            self::$instance = new JoueurControleur();
        }
        return self::$instance;
    }

    public function listerTousLesJoueurs(): array {
        $reponse = api_get('/joueurs');
        return $reponse['data'] ?? [];
    }

    public function getJoueurById(int $id): ?array {
        $reponse = api_get('/joueurs/' . $id);
        return $reponse['data'] ?? null;
    }

    public function ajouterJoueur(
        string $nom,
        string $prenom,
        string $numeroDeLicence,
        string $dateDeNaissance,
        int $tailleEnCm,
        int $poidsEnKg,
        string $statut
    ): bool {
        $donnees = [
            'id' => 0,
            'nom' => $nom,
            'prenom' => $prenom,
            'numero_licence' => $numeroDeLicence,
            'date_naissance' => $dateDeNaissance,
            'taille' => $tailleEnCm,
            'poids' => $poidsEnKg,
            'statut' => $statut,
        ];
        $reponse = api_post('/joueurs', $donnees);
        return isset($reponse['status_code']) && $reponse['status_code'] === 201;
    }

    public function modifierJoueur(
        int $id,
        string $nom,
        string $prenom,
        string $numeroDeLicence,
        string $dateDeNaissance,
        int $tailleEnCm,
        int $poidsEnKg,
        string $statut
    ): bool {
        $donnees = [
            'id' => $id,
            'nom' => $nom,
            'prenom' => $prenom,
            'numero_licence' => $numeroDeLicence,
            'date_naissance' => $dateDeNaissance,
            'taille' => $tailleEnCm,
            'poids' => $poidsEnKg,
            'statut' => $statut,
        ];
        $reponse = api_put('/joueurs/' . $id, $donnees);
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }

    public function supprimerJoueur(int $id): bool {
        $reponse = api_delete('/joueurs/' . $id);
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }

    public function rechercherLesJoueurs(string $recherche, string $statut): array {
        $joueurs = $this->listerTousLesJoueurs();
        return array_values(array_filter($joueurs, function ($joueur) use ($recherche, $statut) {
            $ok = true;
            if ($recherche !== '') {
                $ok = str_contains(strtolower($joueur['nom']), strtolower($recherche))
                   || str_contains(strtolower($joueur['prenom']), strtolower($recherche));
            }
            if ($ok && $statut !== '') {
                $ok = $joueur['statut'] === $statut;
            }
            return $ok;
        }));
    }

    public function listerLesJoueursSelectionnablesPourUnMatch(int $rencontreId): array {
        $joueurs = $this->listerTousLesJoueurs();
        $feuille = ParticipationControleur::getInstance()->getFeuilleDeMatch($rencontreId);
        $idsDejaSurFeuille = array_map(fn($p) => $p['joueur']['id'], $feuille);

        return array_values(array_filter($joueurs, function ($joueur) use ($idsDejaSurFeuille) {
            return $joueur['statut'] === 'ACTIF'
                && !in_array($joueur['id'], $idsDejaSurFeuille);
        }));
    }
}
