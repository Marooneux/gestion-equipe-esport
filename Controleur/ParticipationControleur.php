<?php
namespace R301\Controleur;

use R301\Modele\Participation\FeuilleDeMatch;
use R301\Modele\Participation\Participation;
use R301\Modele\Participation\ParticipationDAO;
use R301\Modele\Participation\Performance;
use R301\Modele\Participation\Poste;
use R301\Modele\Participation\TitulaireOuRemplacant;

class ParticipationControleur {
    private static ?ParticipationControleur $instance = null;
    private readonly ParticipationDAO $participations;
    private readonly JoueurControleur $joueurs;
    private readonly RencontreControleur $rencontres;

    private function __construct(JoueurControleur $joueurs) {
        $this->joueurs = $joueurs;
        $this->participations = ParticipationDAO::getInstance();
        $this->rencontres = RencontreControleur::getInstance();
    }

    public static function getInstance(): ParticipationControleur {
        if (self::$instance == null) {
            self::$instance = new ParticipationControleur(JoueurControleur::getInstance());
        }
        return self::$instance;
    }

    //Cette méthode permet de briser la dépendance cyclique entre les deux controleurs
    public static function getInstanceFromJoueurControleur(JoueurControleur $joueurs): ParticipationControleur {
        if (self::$instance == null) {
            self::$instance = new ParticipationControleur($joueurs);
        }
        return self::$instance;
    }

    public function lejoueurEstDejaSurLaFeuilleDeMatch(int $rencontreId, int $joueurId) : bool {
        return $this->participations->lejoueurEstDejaSurLaFeuilleDeMatch($rencontreId, $joueurId);
    }

    public function listerToutesLesParticipations() : array {
        return $this->participations->selectAllParticipations();
    }

    public function getFeuilleDeMatch(int $rencontreId) : FeuilleDeMatch {
        return new FeuilleDeMatch($this->participations->selectParticipationsByRencontreId($rencontreId));
    }

    public function assignerUnParticipant (
        int $joueurId,
        int $rencontreId,
        Poste $poste,
        TitulaireOuRemplacant $titulaireOuRemplacant
    ) : bool {
        if ($this->participations->lePosteEstDejaOccupe($rencontreId, $poste, $titulaireOuRemplacant)
            || $this->lejoueurEstDejaSurLaFeuilleDeMatch($rencontreId, $joueurId)
        ) {
            return false;
        } else {
            $joueur = $this->joueurs->getJoueurById($joueurId);
            $rencontre = $this->rencontres->getRencontreById($rencontreId);

            $participationACreer = new Participation(
                0,
                $joueur,
                $rencontre,
                $titulaireOuRemplacant,
                null,
                $poste
            );

            return $this->participations->insertParticipation($participationACreer);
        }
    }

    public function assignerUnParticipantByArray(Participation $participantAAjouter) {
        $joueurId = $participantAAjouter->getParticipant()->getJoueurId();
        $rencontreId = $participantAAjouter->getRencontre()->getRencontreId();
        $poste = $participantAAjouter->getPoste();
        $titulaireOuRemplacant = $participantAAjouter->getTitulaireOuRemplacant();
        if ($this->participations->lePosteEstDejaOccupe($rencontreId, $poste, $titulaireOuRemplacant)
            || $this->lejoueurEstDejaSurLaFeuilleDeMatch($rencontreId, $joueurId)
        ) {
            return false;
        }

        return $this->participations->insertParticipation($participantAAjouter);
    }

    public function modifierParticipation(
        int $participationId,
        Poste $poste,
        TitulaireOuRemplacant $titulaireOuRemplacant,
        int $joueurId
    ) : bool {
        $participationAModifier = $this->participations->selectParticipationById($participationId);

        if ($participationAModifier->getParticipant()->getJoueurId() != $joueurId) {
            $participationAModifier->setParticipant($this->joueurs->getJoueurById($joueurId));
        }

        $participationAModifier->setPoste($poste);
        $participationAModifier->setTitulaireOuRemplacant($titulaireOuRemplacant);

        return $this->participations->updateParticipation($participationAModifier);
    }

    public function modifierParticipationByArray(Participation $participationAModifier) {
        $joueurId = $participationAModifier->getParticipant()->getJoueurId();
        $poste = $participationAModifier->getPoste();
        $titulaireOuRemplacant = $participationAModifier->getTitulaireOuRemplacant();

        if ($participationAModifier->getParticipant()->getJoueurId() != $joueurId) {
            $participationAModifier->setParticipant($this->joueurs->getJoueurById($joueurId));
        }

        $this->participations->updatePerformance($participationAModifier);

        $participationAModifier->setPoste($poste);
        $participationAModifier->setTitulaireOuRemplacant($titulaireOuRemplacant);

        return $this->participations->updateParticipation($participationAModifier);
    } 

    public function supprimerLaParticipation(int $participationId) : bool {
        return $this->participations->deleteParticipation($participationId);
    }

    public function mettreAJourLaPerformance(
        Participation $participationAEvaluer,
    ) : bool {

        if (!$participationAEvaluer->getRencontre()->estPassee()) {
            return false;
        }
        return $this->participations->updatePerformance($participationAEvaluer);
    }

    public function supprimerLaPerformance(Participation $participationAEvaluer) : bool {

        if (!$participationAEvaluer->getRencontre()->estPassee()) {
            return false;
        }

        return $this->participations->updatePerformance($participationAEvaluer);
    }

    public function buildParticipationFromArray($data) {
        $joueur = $this->joueurs->getJoueurById($data['joueur_id']);
        $rencontre = $this->rencontres->getRencontreById($data['rencontre_id']);

        return new Participation(
            $data['id'] ?? 0,
            $joueur,
            $rencontre,
            TitulaireOuRemplacant::fromName($data['titularité']),
            isset($data['performance']) == true ? Performance::fromName($data['performance']) : null,
            Poste::fromName($data['poste'])
        );
    }
}