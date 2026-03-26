<?php

namespace R301\Controleur;

use DateTime;
use R301\Modele\Joueur\Joueur;
use R301\Modele\Joueur\JoueurStatut;

require_once __DIR__ . '/ApiClient.php';

class JoueurControleur {
    private static ?JoueurControleur $instance = null;
    private readonly ParticipationControleur $participationControleur;

    private function __construct() {
        $this->participationControleur = ParticipationControleur::getInstanceFromJoueurControleur($this);
    }

    public static function getInstance(): JoueurControleur {
        if (self::$instance == null) {
            self::$instance = new JoueurControleur();
        }
        return self::$instance;
    }

    private function buildJoueurFromData(array $data): Joueur {
        return new Joueur(
            $data['id'],
            $data['nom'],
            $data['prenom'],
            $data['numero_licence'],
            new DateTime($data['date_naissance']),
            $data['taille'],
            $data['poids'],
            isset($data['statut']) ? JoueurStatut::fromName($data['statut']) : null
        );
    }

    public function ajouterJoueur(
        string $nom,
        string $prenom,
        string $numeroDeLicence,
        DateTime $dateDeNaissance,
        int $tailleEnCm,
        int $poidsEnKg,
        string $statut
    ) : bool {
        $joueurACreer = new Joueur(
            0,
            $nom,
            $prenom,
            $numeroDeLicence,
            $dateDeNaissance,
            $tailleEnCm,
            $poidsEnKg,
            JoueurStatut::fromName($statut)
        );

        return $this->ajouterJoueurFromArray($joueurACreer);
    }

    public function ajouterJoueurFromArray(Joueur $joueurACreer): bool {
        $reponse = api_post('/joueurs', $joueurACreer->jsonSerialize());
        return isset($reponse['status_code']) && $reponse['status_code'] === 201;
    }

    public function getJoueurById(int $joueurId): ?Joueur {
        $reponse = api_get('/joueurs/' . $joueurId);
        if (!isset($reponse['data'])) return null;
        return $this->buildJoueurFromData($reponse['data']);
    }

    public function listerLesJoueursSelectionnablesPourUnMatch(int $rencontreId) : array {
        $tousLesJoueurs = $this->listerTousLesJoueurs();
        $feuille = $this->participationControleur->getFeuilleDeMatch($rencontreId);
        $joueursDejaSurFeuille = array_map(
            fn($p) => $p->getParticipant()->getJoueurId(),
            $feuille->getParticipants()
        );

        $joueursSelectionnables = [];
        foreach ($tousLesJoueurs as $joueur) {
            if (
                $joueur->getStatut() === JoueurStatut::ACTIF
                && !in_array($joueur->getJoueurId(), $joueursDejaSurFeuille)
            ) {
                $joueursSelectionnables[] = $joueur;
            }
        }

        return $joueursSelectionnables;
    }

    public function listerTousLesJoueurs() : array {
        $reponse = api_get('/joueurs');
        if (!isset($reponse['data']) || !is_array($reponse['data'])) return [];
        return array_map(fn($d) => $this->buildJoueurFromData($d), $reponse['data']);
    }

    public function modifierJoueur(
        int $joueurId,
        string $nom,
        string $prenom,
        string $numeroDeLicence,
        DateTime $dateDeNaissance,
        int $tailleEnCm,
        int $poidsEnKg,
        string $statut
    ) : bool {
        $joueurAModifier = new Joueur(
            $joueurId,
            $nom,
            $prenom,
            $numeroDeLicence,
            $dateDeNaissance,
            $tailleEnCm,
            $poidsEnKg,
            JoueurStatut::fromName($statut)
        );

        return $this->modifierJoueurByArray($joueurAModifier);
    }

    public function modifierJoueurByArray(Joueur $joueurAModifier) : bool {
        $reponse = api_put('/joueurs/' . $joueurAModifier->getJoueurId(), $joueurAModifier->jsonSerialize());
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }

    public function rechercherLesJoueurs(string $recherche, string $statut) : array {
        $tousLesJoueurs = $this->listerTousLesJoueurs();
        $joueursTrouves = [];

        foreach ($tousLesJoueurs as $joueur) {
            $conserverDansLaListe = true;

            if ($recherche !== "") {
                $conserverDansLaListe = $joueur->nomOuPrenomContient($recherche);
            }

            if ($conserverDansLaListe && $statut !== "") {
                $conserverDansLaListe = $joueur->getStatut() == JoueurStatut::fromName($statut);
            }

            if ($conserverDansLaListe) {
                $joueursTrouves[] = $joueur;
            }
        }

        return $joueursTrouves;
    }

    public function supprimerJoueur(int $joueurId) : bool {
        $reponse = api_delete('/joueurs/' . $joueurId);
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }
}
