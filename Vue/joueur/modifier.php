<h1>Modifier un joueur</h1>
<?php
use R301\Controleur\JoueurControleur;

if (!isset($_GET['id'])) {
    header('Location: /joueur');
    exit;
}

$id = $_GET['id'];
$joueurControleur = JoueurControleur::getInstance();
$joueur = $joueurControleur->getJoueurById((int) $id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dateDeNaissance = new DateTime($_POST['dateDeNaissance']);
    $result = $joueurControleur->modifierJoueur(
        (int) $id,
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['numeroDeLicence'],
        $dateDeNaissance,
        (int) $_POST['tailleEnCm'],
        (int) $_POST['poidsEnKg'],
        $_POST['statut']
    );
    if ($result) {
        header('Location: /joueur');
        exit;
    }
}
?>

<div class="container">
    <form action="/joueur/modifier?id=<?= $id ?>" method="post">
        <div class="row">
            <div class="col-20"><label for="nom">Nom</label></div>
            <div class="col-80"><input type="text" id="nom" name="nom" value="<?= $joueur->getNom() ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="prenom">Prenom</label></div>
            <div class="col-80"><input type="text" id="prenom" name="prenom" value="<?= $joueur->getPrenom() ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="numeroDeLicence">Numéro de license</label></div>
            <div class="col-80"><input type="text" id="numeroDeLicence" name="numeroDeLicence" value="<?= $joueur->getNumeroDeLicence() ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="dateDeNaissance">Date de naissance</label></div>
            <div class="col-80"><input type="date" id="dateDeNaissance" name="dateDeNaissance" value="<?= $joueur->getDateDeNaissance()->format('Y-m-d') ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="tailleEnCm">Taille (en cm)</label></div>
            <div class="col-80"><input type="text" id="tailleEnCm" name="tailleEnCm" value="<?= $joueur->getTailleEnCm() ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="poidsEnKg">Poids (en Kg)</label></div>
            <div class="col-80"><input type="text" id="poidsEnKg" name="poidsEnKg" value="<?= $joueur->getPoidsEnKg() ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="statut">Statut</label></div>
            <div class="col-80">
                <select id="statut" name="statut">
                    <?php foreach (['ACTIF', 'BLESSE', 'ABSENT', 'SUSPENDU'] as $s) { ?>
                        <option value="<?= $s ?>" <?= $joueur->getStatut()->name === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="row">
            <input class="update" type="submit" value="Modifier">
        </div>
    </form>
</div>
