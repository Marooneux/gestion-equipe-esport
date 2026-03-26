<?php

use R301\Controleur\JoueurControleur;
use R301\Controleur\ParticipationControleur;
use R301\Vue\Component\Select;

$controleur = ParticipationControleur::getInstance();
$joueurControleur = JoueurControleur::getInstance();

if (!isset($_GET['id'])) :
    header("Location: /rencontre");
else :
    $feuille = $controleur->getFeuilleDeMatch((int) $_GET['id']);
    $joueursSelectionnables = $joueurControleur->listerLesJoueursSelectionnablesPourUnMatch((int) $_GET['id']);

    function joueurToString(array $joueur): string {
        $str = $joueur['numero_licence'] . ' : ' . $joueur['nom'] . ' ' . $joueur['prenom'];
        if ($joueur['statut'] !== 'ACTIF') {
            $str .= ' (' . $joueur['statut'] . ')';
        }
        return $str;
    }
?>
<div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding-right: 30px">
    <h1>Feuille de Match</h1>
    <?php if($controleur->feuilleEstComplete($feuille)) : ?>
    <div class="etat-feuille-de-match feuille-de-match-complete">
        COMPLÈTE
    </div>
    <?php else: ?>
    <div class="etat-feuille-de-match feuille-de-match-incomplete">
        INCOMPLÈTE
    </div>
    <?php endif; ?>
</div>

<div class="container" style="display: flex; flex-direction: row; justify-content: space-between">
    <?php foreach (['TITULAIRE', 'REMPLACANT'] as $titulaireOuRemplacant) : ?>
    <table style="width: 49.5%">
        <caption>
            <?php echo $titulaireOuRemplacant . 'S' ?>
        </caption>
        <tr>
            <th style="width:15%">Poste</th>
            <th style="width:30%">Joueur</th>
            <th style="width:35%">Sélectionner un joueur</th>
            <th style="width:20%; min-width: 150px;"></th>
        </tr>

        <?php
            foreach (['TOPLANE', 'JUNGLE', 'MIDLANE', 'ADCARRY', 'SUPPORT'] as $poste):
                $participant = $controleur->getParticipantAuPoste($feuille, $poste, $titulaireOuRemplacant);

                $selectableValues = [];
                foreach ($joueursSelectionnables as $j) {
                    $selectableValues[$j['id']] = joueurToString($j);
                }
                if ($participant !== null) {
                    $selectableValues[$participant['joueur']['id']] = joueurToString($participant['joueur']);
                }

                $selectedValue = $participant !== null ? joueurToString($participant['joueur']) : null;
                $select = new Select($selectableValues, "joueurId", null, $selectedValue);
        ?>
        <form action="/feuilleDeMatch/modifier" method="post">
            <tr>
                <input type="hidden" name="participationId" value="<?php if($participant !== null) echo $participant['id']; ?>" />
                <input type="hidden" name="poste" value="<?php echo $poste ?>" />
                <input type="hidden" name="rencontreId" value="<?php echo $_GET['id'] ?>" />
                <input type="hidden" name="titulaireOuRemplacant" value="<?php echo $titulaireOuRemplacant ?>" />
                <td><?php echo $poste; ?></td>
                <td><?php if($participant !== null) echo joueurToString($participant['joueur']); ?></td>
                <td><?php $select->toHTML(); ?></td>
                <td class="actions">
                    <?php if($participant !== null) : ?>
                    <button class="update" type="submit" name="action" value="update">Modifier</button>
                    <button class="delete" type="submit" name="action" value="delete" style="margin-left: 8px">Supprimer</button>
                    <?php else: ?>
                    <button class="create" type="submit" name="action" value="create">Assigner</button>
                    <?php endif; ?>
                </td>
            </tr>
        </form>
        <?php endforeach; ?>
    </table>
    <?php endforeach; ?>
</div>
<?php endif; ?>
