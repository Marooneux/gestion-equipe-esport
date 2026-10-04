<?php

use R301\Controleur\CommentaireControleur;
use R301\Controleur\JoueurControleur;
use R301\Vue\Component\Formulaire;

if (!isset($_GET['id'])) {
    header('Location: /joueur');
    die();
}

$joueurId = (int) $_GET['id'];
$joueur = JoueurControleur::getInstance()->getJoueurById($joueurId);
$joueurStr = $joueur['numero_licence'] . ' : ' . $joueur['nom'] . ' ' . $joueur['prenom'];
?>

<h1>Commentaires de <?php echo $joueurStr; ?></h1>

<?php
$form = new Formulaire("commentaire/ajouter");
$form->addTextArea("contenu");
$form->addHiddenInput("joueurId", $joueurId);
$form->addButton("submit", "create", "Publier le commentaire", "Publier le commentaire");
echo $form;

$commentaires = CommentaireControleur::getInstance()->listerLesCommentairesDuJoueur($joueurId);
usort($commentaires, fn($a, $b) => $b['date'] <=> $a['date']);
?>
<div class="container">
    <table>
        <tr>
            <th style="min-width: 100px; width: 1%">Date</th>
            <th style="width: 80%">Commentaire</th>
            <th style="width: 1%"></th>
        </tr>
        <?php foreach ($commentaires as $commentaire): ?>
        <form action="/joueur/commentaire/supprimer" method="post">
            <input type="hidden" name="commentaireId" value="<?php echo $commentaire['id']; ?>" />
            <input type="hidden" name="joueurId" value="<?php echo $joueurId; ?>" />
            <tr>
                <td><?php echo date('d/m/Y H:i', strtotime($commentaire['date'])); ?></td>
                <td><?php echo htmlspecialchars($commentaire['contenu']); ?></td>
                <td class="actions">
                    <button class="delete" type="submit">Supprimer</button>
                </td>
            </tr>
        </form>
        <?php endforeach; ?>
    </table>
</div>
