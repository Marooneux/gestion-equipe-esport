<?php

namespace R301\Controleur;

use DateTime;
use R301\Modele\Joueur\Commentaire\Commentaire;
use R301\Modele\Joueur\Commentaire\CommentaireDAO;
use R301\Modele\Joueur\Joueur;
use R301\Modele\Joueur\JoueurDAO;
use R301\Modele\Joueur\JoueurStatut;
use R301\Modele\Statistiques\StatistiquesEquipe;
use R301\Modele\Statistiques\StatistiquesJoueurs;

class StatistiquesControleur {
    private static ?StatistiquesControleur $instance = null;
    private readonly JoueurControleur $joueurs;
    private readonly RencontreControleur $rencontres;
    private readonly ParticipationControleur $participations;

    private function __construct() {
        $this->joueurs = JoueurControleur::getInstance();
        $this->rencontres = RencontreControleur::getInstance();
        $this->participations = ParticipationControleur::getInstance();
    }

    public static function getInstance(): StatistiquesControleur {
        if (self::$instance == null) {
            self::$instance = new StatistiquesControleur();
        }
        return self::$instance;
    }

    public function getStatistiquesEquipe() : StatistiquesEquipe {
        return new StatistiquesEquipe($this->rencontres->listerToutesLesRencontres());
    }

    public function getStatistiquesJoueurs() : StatistiquesJoueurs {
        return new StatistiquesJoueurs($this->participations->listerToutesLesParticipations(), $this->rencontres->listerToutesLesRencontres());
    }

    private function buildStatistiquesPourJoueur(Joueur $joueur, StatistiquesJoueurs $statistiquesJoueurs): array {
        return [
            'joueur_id' => $joueur->getJoueurId(),
            'nom' => $joueur->getNom(),
            'prenom' => $joueur->getPrenom(),
            'nb_matchs_joues' => $statistiquesJoueurs->nbMatchsJoues($joueur),
            'nb_matchs_gagnes' => $statistiquesJoueurs->nbMatchsGagnes($joueur),
            'nb_matchs_evalues' => $statistiquesJoueurs->nbMatchsEvalues($joueur),
            'nb_titularisations' => $statistiquesJoueurs->nbTitularisations($joueur),
            'nb_remplacant' => $statistiquesJoueurs->nbRemplacant($joueur),
            'moyenne_evaluations' => $statistiquesJoueurs->moyenneDesEvaluations($joueur),
            'pourcentage_matchs_gagnes' => $statistiquesJoueurs->pourcentageDeMatchsGagnes($joueur),
            'poste_le_plus_performant' => $statistiquesJoueurs->posteLePlusPerformant($joueur)?->name,
            'nb_rencontres_consecutives' => $statistiquesJoueurs->nbRencontresConsecutivesADate($joueur)
        ];
    }

    public function getStatistiquesTousLesJoueurs(): array {
        $joueurs = $this->joueurs->listerTousLesJoueurs();
        $statistiquesJoueurs = $this->getStatistiquesJoueurs();

        return array_map(function (Joueur $joueur) use ($statistiquesJoueurs) {
            return $this->buildStatistiquesPourJoueur($joueur, $statistiquesJoueurs);
        }, $joueurs);
    }

    public function getStatistiquesDUnJoueur(int $joueurId): array|false {
        $joueur = $this->joueurs->getJoueurById($joueurId);
        if ($joueur === false) {
            return false;
        }

        return $this->buildStatistiquesPourJoueur($joueur, $this->getStatistiquesJoueurs());
    }
}
