<?php
session_start();

require_once __DIR__ . '/includes/photos.php';

// 1) Connexion MySQL (XAMPP : user root, mot de passe vide)
$conn = mysqli_connect('localhost', 'root', 'root', 'bd_blogmoteur');
$sql_injection = '';
$exclure_mes_annonces = '';

if (isset($_SESSION['id_user'])) {
    $exclure_mes_annonces = ' AND a.id_user != ' . (int) $_SESSION['id_user'];
}

if ($conn) {
    // F8 (Omar) : construction des conditions SQL à partir des filtres GET
    require __DIR__ . '/filtres-where.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des annonces — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/site.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="site-main">
    <h1>Liste des annonces</h1>

    <?php if ($conn) { include __DIR__ . '/form-filtres.php'; } ?>

    <div class="grille">
<?php
if (!$conn) {
    echo '<p class="erreur">Erreur de connexion MySQL. Vérifiez que MySQL est démarré dans XAMPP (bouton vert), puis rechargez la page.</p>';
} else {
    // 2) Requête : annonce + catégorie + 1ère photo (ordre = 1) + filtres Omar
    $sql = "SELECT a.id_anno, a.titre, a.prix, a.description, a.localisation,
                   c.nom AS categorie, p.chemin
            FROM annonces a
            INNER JOIN categories c ON c.id_cat = a.id_cat
            LEFT JOIN photos p ON p.id_anno = a.id_anno AND p.ordre = 1
            WHERE 1=1" . $sql_injection . $exclure_mes_annonces . "
            ORDER BY a.created_at DESC";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo '<p class="erreur">Erreur SQL : ' . htmlspecialchars(mysqli_error($conn)) . '</p>';
    } elseif (mysqli_num_rows($result) === 0) {
        echo '<p class="vide">Aucune annonce trouvée.</p>';
    } else {
        // 3) Une ligne SQL = une carte
        while ($ligne = mysqli_fetch_assoc($result)) {
            $idAnno = (int) $ligne['id_anno'];
            $img = chemin_photo_vignette($ligne['chemin']);

            // Description courte sur la carte (pas tout le texte)
            $desc = $ligne['description'];
            if (strlen($desc) > 100) {
                $desc = substr($desc, 0, 100) . '…';
            }

            echo '<article class="carte">';
            // F7 : clic → page détail avec l'id dans l'URL
            echo '<a href="detail_annonce.php?id_anno=' . $idAnno . '">';
            if ($img !== '') {
                echo '<img src="' . $img . '" alt="' . htmlspecialchars($ligne['titre']) . '" loading="lazy" decoding="async">';
            } else {
                echo '<div class="carte__sans-photo">Pas de photo</div>';
            }
            echo '<h2>' . htmlspecialchars($ligne['titre']) . '</h2>';
            echo '<p class="prix">' . number_format((float) $ligne['prix'], 2, ',', ' ') . ' €</p>';
            echo '<p class="desc">' . htmlspecialchars($desc) . '</p>';
            echo '<p class="meta">' . htmlspecialchars($ligne['categorie']) . ' — '
                . htmlspecialchars($ligne['localisation']) . '</p>';
            echo '</a></article>';
        }
    }

    mysqli_close($conn);
}
?>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
