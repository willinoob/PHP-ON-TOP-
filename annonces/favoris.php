<?php
// F9 (Omar) — page « Mes favoris »
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: ../mon_compte/connexion.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include __DIR__ . '/../includes/favicon.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes favoris — Le Blog Moteur</title>
    <link rel="stylesheet" href="../assets/css/base.css?v=<?= filemtime(__DIR__ . '/../assets/css/base.css') ?>">
    <link rel="stylesheet" href="../assets/css/favoris.css?v=<?= filemtime(__DIR__ . '/../assets/css/favoris.css') ?>">
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="site-main">
    <h1>Mes annonces favorites</h1>

    <?php if (!$conn): ?>
        <p class="erreur">Erreur de connexion à la base de données.</p>
    <?php else: ?>
        <div class="favoris-table-wrap">
            <table class="favoris-table">
                <thead>
                    <tr>
                        <th>Annonce</th>
                        <th>Prix</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Jointure favoris + annonces pour afficher titre et prix
                $sql = "SELECT f.id_anno, a.titre, a.prix
                        FROM favoris f
                        INNER JOIN annonces a ON f.id_anno = a.id_anno
                        WHERE f.id_user = $id_user
                        ORDER BY f.created_at DESC";

                $resultat = mysqli_query($conn, $sql);

                if (!$resultat || mysqli_num_rows($resultat) === 0):
                ?>
                    <tr>
                        <td colspan="3" class="favoris-table__vide">
                            Vous n'avez pas encore d'annonces en favoris.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php while ($ligne = mysqli_fetch_assoc($resultat)): ?>
                        <tr>
                            <td>
                                <a href="detail_annonce.php?id_anno=<?= (int) $ligne['id_anno'] ?>">
                                    <?= htmlspecialchars($ligne['titre']) ?>
                                </a>
                            </td>
                            <td><?= number_format((float) $ligne['prix'], 2, ',', ' ') ?> €</td>
                            <td>
                                <form action="toggle-favori.php" method="post" class="favoris-table__form">
                                    <input type="hidden" name="id_anno" value="<?= (int) $ligne['id_anno'] ?>">
                                    <button type="submit" class="favoris-table__btn-retirer">Retirer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php mysqli_close($conn); ?>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
