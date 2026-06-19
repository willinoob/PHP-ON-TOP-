<?php
/**
 * Mes annonces — liste des annonces de l'utilisateur + modifier / supprimer.
 */
session_start();

require_once __DIR__ . '/includes/photos.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];

$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');
$annonces = [];
$erreur = null;

if (!$conn) {
    $erreur = 'Erreur de connexion : ' . mysqli_connect_error();
} else {
    $sql = "SELECT a.id_anno, a.titre, a.prix, a.description, a.localisation, a.vues,
                   c.nom AS categorie, p.chemin
            FROM annonces a
            INNER JOIN categories c ON c.id_cat = a.id_cat
            LEFT JOIN photos p ON p.id_anno = a.id_anno AND p.ordre = 1
            WHERE a.id_user = " . $id_user . "
            ORDER BY a.created_at DESC";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        $erreur = 'Erreur SQL : ' . mysqli_error($conn);
    } else {
        while ($ligne = mysqli_fetch_assoc($result)) {
            $annonces[] = $ligne;
        }
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include __DIR__ . '/includes/favicon.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes annonces — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/base.css?v=<?= filemtime(__DIR__ . '/assets/css/base.css') ?>">
    <link rel="stylesheet" href="assets/css/mes_annonces.css?v=<?= filemtime(__DIR__ . '/assets/css/mes_annonces.css') ?>">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="site-main">
    <h1>Mes annonces</h1>
   
    <?php if ($erreur !== null): ?>
        <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php elseif (count($annonces) === 0): ?>
        <p class="vide">Vous n'avez pas encore déposé d'annonce.</p>
    <?php else: ?>

        <div class="grille mes-annonces__grille">
            <?php foreach ($annonces as $ligne):
                $idAnno = (int) $ligne['id_anno'];
                $img = chemin_photo_vignette($ligne['chemin']);
                $desc = $ligne['description'];
                if (strlen($desc) > 100) {
                    $desc = substr($desc, 0, 100) . '…';
                }
            ?>
            <article class="carte carte--sans-lien">
                <?php if ($img !== ''): ?>
                    <img src="<?= $img ?>" alt="<?= htmlspecialchars($ligne['titre']) ?>" loading="lazy" decoding="async">
                <?php else: ?>
                    <div class="carte__sans-photo">Pas de photo</div>
                <?php endif; ?>
                <h2><?= htmlspecialchars($ligne['titre']) ?></h2>
                <p class="prix"><?= number_format((float) $ligne['prix'], 2, ',', ' ') ?> €</p>
                <p class="desc"><?= htmlspecialchars($desc) ?></p>
                <p class="meta"><?= htmlspecialchars($ligne['categorie']) ?> — <?= htmlspecialchars($ligne['localisation']) ?> — <?= (int) $ligne['vues'] ?> vue(s)</p>
                <div class="carte__actions">
                    <a href="formulaire_modif_annonces.php?id_anno=<?= $idAnno ?>" class="btn-valider">
                        Modifier annonce
                    </a>
                    <!-- confirm() : demande confirmation avant d'envoyer le formulaire de suppression -->
                    <form method="post" action="supprimer_annonce.php" class="carte__form-supprimer"
                          onsubmit="return confirm('Supprimer cette annonce ? Cette action est irréversible.');">
                        <input type="hidden" name="id_anno" value="<?= $idAnno ?>">
                        <button type="submit" class="btn-supprimer">Supprimer</button>
                    </form>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
