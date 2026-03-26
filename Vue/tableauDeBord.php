<?php

use R301\Controleur\JoueurControleur;
use R301\Controleur\StatistiquesControleur;

$controleur = StatistiquesControleur::getInstance();
$statistiquesEquipe = $controleur->getStatistiquesEquipe();
$joueurs = JoueurControleur::getInstance()->listerTousLesJoueurs();

?>

<div class="TripleGrid">
    <div>
        <h1><?php echo $statistiquesEquipe['nbVictoires']; ?></h1>
        <p> matchs gagnés</p>
    </div>
    <div>
        <h1><?php echo $statistiquesEquipe['nbNuls']; ?></h1>
        <p> matchs nuls</p>
    </div>
    <div>
        <h1><?php echo $statistiquesEquipe['nbDefaites']; ?></h1>
        <p> matchs perdus</p>
    </div>
    <div>
        <h1><?php echo $statistiquesEquipe['pourcentageVictoires']; ?>%</h1>
        <p> de matchs gagnés</p>
    </div>
    <div>
        <h1><?php echo $statistiquesEquipe['pourcentageNuls']; ?>%</h1>
        <p> de matchs nuls</p>
    </div>
    <div>
        <h1><?php echo $statistiquesEquipe['pourcentageDefaites']; ?>%</h1>
        <p> de matchs perdus</p>
    </div>
</div>
<div class="overflow">
    <table>
        <tr>
            <th style="width:15%;">Joueur</th>
            <th style="width:7%;">Statut</th>
            <th style="width:7%;">Poste le plus performant</th>
            <th style="width:7%;">Nombre de matchs consécutifs</th>
            <th style="width:7%;">Nombre titularisations</th>
            <th style="width:7%;">Nombre remplaçants</th>
            <th style="width:7%;">Moyenne évaluations</th>
            <th style="width:7%;">Pourcentage gagnés</th>
        </tr>
        <?php foreach ($joueurs as $joueur): ?>
        <?php $stats = $controleur->getStatistiquesParJoueur($joueur); ?>
        <tr>
            <td><?php echo $joueur['numero_licence'] . ' : ' . $joueur['nom'] . ' ' . $joueur['prenom']; ?></td>
            <td><?php echo $joueur['statut']; ?></td>
            <td><?php echo $stats['posteLePlusPerformant']; ?></td>
            <td><?php echo $stats['nbConsecutifs']; ?></td>
            <td><?php echo $stats['nbTitularisations']; ?></td>
            <td><?php echo $stats['nbRemplacants']; ?></td>
            <td><?php echo $stats['moyenneEvaluations']; ?></td>
            <td><?php echo $stats['pourcentageGagnes']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
