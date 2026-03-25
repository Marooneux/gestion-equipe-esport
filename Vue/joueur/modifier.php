<h1>Modifier un joueur</h1>
<?php
require_once __DIR__ . '/../../Controleur/ApiClient.php';

if (!isset($_GET['id'])) {
    header('Location: /joueur');
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donnees = [
        'id' => $id,
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'numero_licence' => $_POST['numeroDeLicence'],
        'date_naissance' => $_POST['dateDeNaissance'],
        'taille' => (int) $_POST['tailleEnCm'],
        'poids' => (int) $_POST['poidsEnKg'],
        'statut' => $_POST['statut'],
    ];
    $reponse = api_put('/joueurs/' . $id, $donnees);
    if ($reponse['status_code'] === 200) {
        header('Location: /joueur');
        exit;
    }
}

$reponse = api_get('/joueurs/' . $id);
$joueur = $reponse['data'];
?>

<div class="container">
    <form action="/joueur/modifier?id=<?= $id ?>" method="post">
        <div class="row">
            <div class="col-20"><label for="nom">Nom</label></div>
            <div class="col-80"><input type="text" id="nom" name="nom" value="<?= $joueur['nom'] ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="prenom">Prenom</label></div>
            <div class="col-80"><input type="text" id="prenom" name="prenom" value="<?= $joueur['prenom'] ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="numeroDeLicence">Numéro de license</label></div>
            <div class="col-80"><input type="text" id="numeroDeLicence" name="numeroDeLicence" value="<?= $joueur['numero_licence'] ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="dateDeNaissance">Date de naissance</label></div>
            <div class="col-80"><input type="date" id="dateDeNaissance" name="dateDeNaissance" value="<?= $joueur['date_naissance'] ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="tailleEnCm">Taille (en cm)</label></div>
            <div class="col-80"><input type="text" id="tailleEnCm" name="tailleEnCm" value="<?= $joueur['taille'] ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="poidsEnKg">Poids (en Kg)</label></div>
            <div class="col-80"><input type="text" id="poidsEnKg" name="poidsEnKg" value="<?= $joueur['poids'] ?>" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="statut">Statut</label></div>
            <div class="col-80">
                <select id="statut" name="statut">
                    <?php foreach (['ACTIF', 'BLESSE', 'ABSENT', 'SUSPENDU'] as $s) { ?>
                        <option value="<?= $s ?>" <?= $joueur['statut'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="row">
            <input class="update" type="submit" value="Modifier">
        </div>
    </form>
</div>
