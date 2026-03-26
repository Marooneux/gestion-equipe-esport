<?php

namespace R301\Controleur;

require_once __DIR__ . '/ApiClient.php';

class StatistiquesControleur {
    private static ?StatistiquesControleur $instance = null;
    private array $participations;
    private array $rencontres;

    private function __construct() {
        $this->participations = api_get('/feuilledematche')['data'] ?? [];
        $this->rencontres     = api_get('/rencontre')['data'] ?? [];
    }

    public static function getInstance(): StatistiquesControleur {
        if (self::$instance === null) {
            self::$instance = new StatistiquesControleur();
        }
        return self::$instance;
    }

    public function getStatistiquesEquipe(): array {
        $nbVictoires = 0;
        $nbNuls      = 0;
        $nbDefaites  = 0;
        $nbJoues     = 0;

        foreach ($this->rencontres as $r) {
            if ($r['resultat'] === null) continue;
            $nbJoues++;
            if ($r['resultat'] === 'VICTOIRE') $nbVictoires++;
            if ($r['resultat'] === 'NUL')      $nbNuls++;
            if ($r['resultat'] === 'DEFAITE')  $nbDefaites++;
        }

        return [
            'nbVictoires'          => $nbVictoires,
            'nbNuls'               => $nbNuls,
            'nbDefaites'           => $nbDefaites,
            'pourcentageVictoires' => $nbJoues > 0 ? (int)(($nbVictoires / $nbJoues) * 100) : 0,
            'pourcentageNuls'      => $nbJoues > 0 ? (int)(($nbNuls      / $nbJoues) * 100) : 0,
            'pourcentageDefaites'  => $nbJoues > 0 ? (int)(($nbDefaites  / $nbJoues) * 100) : 0,
        ];
    }

    public function getStatistiquesParJoueur(array $joueur): array {
        $joueurId = $joueur['id'];
        $noteParPerformance = ['EXCELLENTE' => 5, 'BONNE' => 4, 'MOYENNE' => 3, 'MAUVAISE' => 2, 'CATASTROPHIQUE' => 1];

        // Participations du joueur dans des matchs joués
        $parts = [];
        foreach ($this->participations as $p) {
            if ($p['joueur']['id'] === $joueurId && $p['rencontre']['resultat'] !== null) {
                $parts[] = $p;
            }
        }

        // Compteurs simples
        $nbTitularisations = 0;
        $nbRemplacants     = 0;
        $nbGagnes          = 0;
        $somme             = 0;
        $nbEvalues         = 0;

        foreach ($parts as $p) {
            if ($p['titularité'] === 'TITULAIRE')           $nbTitularisations++;
            if ($p['titularité'] === 'REMPLACANT')          $nbRemplacants++;
            if ($p['rencontre']['resultat'] === 'VICTOIRE') $nbGagnes++;
            if ($p['performance'] !== null) {
                $somme += $noteParPerformance[$p['performance']];
                $nbEvalues++;
            }
        }

        $nbJoues           = count($parts);
        $moyenne           = $nbEvalues > 0 ? round($somme / $nbEvalues, 2) : null;
        $pourcentageGagnes = $nbJoues  > 0 ? (int)(($nbGagnes / $nbJoues) * 100) : null;

        // Rencontres consécutives (triées par date)
        $rencontresJouees = [];
        foreach ($this->rencontres as $r) {
            if ($r['resultat'] !== null) $rencontresJouees[] = $r;
        }
        usort($rencontresJouees, function ($a, $b) {
            return strtotime($a['date_heure']['date']) <=> strtotime($b['date_heure']['date']);
        });

        $nbConsecutifs = 0;
        foreach ($rencontresJouees as $rencontre) {
            $aParticipe = false;
            foreach ($parts as $p) {
                if ($p['rencontre']['id'] === $rencontre['id']) {
                    $aParticipe = true;
                    break;
                }
            }
            if ($aParticipe) {
                $nbConsecutifs++;
            } else {
                break;
            }
        }

        // Poste le plus performant
        $posteLePlusPerformant = null;
        if ($nbJoues > 0) {
            $moyenneParPoste = [];
            foreach (['TOPLANE', 'JUNGLE', 'MIDLANE', 'ADCARRY', 'SUPPORT'] as $poste) {
                $sommePoste = 0;
                $nbPoste    = 0;
                foreach ($parts as $p) {
                    if ($p['poste'] === $poste && $p['performance'] !== null) {
                        $sommePoste += $noteParPerformance[$p['performance']];
                        $nbPoste++;
                    }
                }
                $moyenneParPoste[$poste] = $nbPoste > 0 ? $sommePoste / $nbPoste : 0;
            }
            arsort($moyenneParPoste);
            $posteLePlusPerformant = array_key_first($moyenneParPoste);
        }

        return [
            'posteLePlusPerformant' => $posteLePlusPerformant,
            'nbConsecutifs'         => $nbConsecutifs,
            'nbTitularisations'     => $nbTitularisations,
            'nbRemplacants'         => $nbRemplacants,
            'moyenneEvaluations'    => $moyenne,
            'pourcentageGagnes'     => $pourcentageGagnes,
        ];
    }
}
