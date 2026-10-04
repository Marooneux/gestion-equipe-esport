<?php

use R301\Controleur\JoueurControleur;

$controleur = JoueurControleur::getInstance();
$joueurs = $controleur->rechercherLesJoueurs(
    $_GET['recherche'] ?? '',
    $_GET['statut'] ?? ''
);

?>

<h1>Joueurs</h1>
<div class="container">
    <form action="joueur" method="get">
        <div class="row">
            <div class="invCol-80">
                <input type="search" name="recherche" placeholder="Rechercher" value="<?= htmlspecialchars($_GET['recherche'] ?? '') ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="invCol-80">
                <select name="statut" id="statut">
                    <option value="">Tous</option>
                    <option value="ACTIF" <?= (isset($_GET['statut']) && $_GET['statut'] === "ACTIF") ? 'selected' : '' ?>>Actif</option>
                    <option value="BLESSE" <?= (isset($_GET['statut']) && $_GET['statut'] === "BLESSE") ? 'selected' : '' ?>>Blessé</option>
                    <option value="ABSENT" <?= (isset($_GET['statut']) && $_GET['statut'] === "ABSENT") ? 'selected' : '' ?>>Absent</option>
                    <option value="SUSPENDU" <?= (isset($_GET['statut']) && $_GET['statut'] === "SUSPENDU") ? 'selected' : '' ?>>Suspendu</option>
                </select>
            </div>
            <div class="invCol-20">
                <input class="filter-button" type="submit" value="Filtrer">
            </div>
        </div>
    </form>
</div>

<div class="overflow container">
    <table style="width: 100%">
        <tr>
            <th style="width:8%">Numero Licence</th>
            <th style="width:12%">Nom</th>
            <th style="width:12%">Prenom</th>
            <th style="width:12%">Date de naissance</th>
            <th style="width:12%">Taille</th>
            <th style="width:12%">Poids</th>
            <th style="width:12%">Statut</th>
            <th style="width:20%; min-width: 370px;">Actions</th>
        </tr>

        <?php foreach ($joueurs as $joueur) { ?>
            <tr>
                <td><?= $joueur['numero_licence'] ?></td>
                <td><?= $joueur['nom'] ?></td>
                <td><?= $joueur['prenom'] ?></td>
                <td><?= date('d/m/Y', strtotime($joueur['date_naissance'])) ?></td>
                <td><?= $joueur['taille'] ?> cm</td>
                <td><?= $joueur['poids'] ?> kg</td>
                <td><?= $joueur['statut'] ?></td>
                <td class="actions">
                    <form action="joueur/modifier" method="get"><button class="update" type="submit" name="id" value="<?= $joueur['id'] ?>">Modifier</button></form>
                    <form action="joueur/supprimer" method="post">
                        <input type="hidden" name="id" value="<?= $joueur['id'] ?>">
                        <button class="delete" type="submit" onclick="return confirm('Voulez-vous vraiment supprimer ce joueur?')">Supprimer</button>
                    </form>
                    <form action="joueur/commentaire" method="get"><button class="info" type="submit" name="id" value="<?= $joueur['id'] ?>">Commentaires</button></form>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php echo count($joueurs) . " joueurs retournés"; ?>
</div>
