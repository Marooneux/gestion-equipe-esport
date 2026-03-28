<?php

namespace R301\Modele\Statistiques;

use R301\Modele\Rencontre\RencontreResultat;

class StatistiquesEquipe implements \JsonSerializable {
    private readonly array $rencontres;

    public function __construct(
        array $rencontres
    ) {
        $this->rencontres = $rencontres;
    }

    private function nbMatchsJoues(): int {
        return count(array_filter($this->rencontres, function($rencontre) { return $rencontre->joue();}));
    }

    public function nbVictoires(): int {
        return count(array_filter($this->rencontres, function($rencontre) { return $rencontre->gagne(); }));
    }

    public function nbNuls(): int {
        return count(array_filter($this->rencontres, function($rencontre) { return $rencontre->nul(); }));
    }

    public function nbDefaites(): int {
        return count(array_filter($this->rencontres, function($rencontre) { return $rencontre->perdu() ;}));
    }

    public function pourcentageDeVictoires(): int {
        if ($this->nbMatchsJoues() === 0) return 0;
        return (int) ($this->nbVictoires() / $this->nbMatchsJoues() * 100);
    }

    public function pourcentageDeNuls(): int {
        if ($this->nbMatchsJoues() === 0) return 0;
        return (int) ($this->nbNuls() / $this->nbMatchsJoues() * 100);
    }

    public function pourcentageDeDefaites(): int {
        if ($this->nbMatchsJoues() === 0) return 0;
        return (int) ($this->nbDefaites() / $this->nbMatchsJoues() * 100);
    }

    public function jsonSerialize(): array {
        return [
            'matchs_joues' => $this->nbMatchsJoues(),
            'victoires' => $this->nbVictoires(),
            'nuls' => $this->nbNuls(),
            'defaites' => $this->nbDefaites(),
            'pourcentage_victoires' => $this->pourcentageDeVictoires(),
            'pourcentage_nuls' => $this->pourcentageDeNuls(),
            'pourcentage_defaites' => $this->pourcentageDeDefaites()
        ];
    }
}


