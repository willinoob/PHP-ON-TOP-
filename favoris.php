<?php
// Omar — F9 : page « Mes favoris »
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

$id_user_connecte = (int) $_SESSION['id_user'];
$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');

function chemin_photo_affichage($chemin) {
    if (empty($chemin)) return '';
    $pos = strrpos($chemin, '/');
    if ($pos === false) return htmlspecialchars(rawurlencode($chemin));
    return htmlspecialchars(substr($chemin, 0, $pos + 1) . rawurlencode(substr($chemin, $pos + 1)));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes favoris — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/site.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="site-main">
    <h1>Mes favoris</h1>

    <div class="grille">
<?php
if (!$conn) {
    echo '<p class="erreur">Erreur de connexion à la base de données.</p>';
} else {
    $sql = "SELECT a.id_anno, a.titre, a.prix, a.description, a.localisation, c.nom AS categorie, p.chemin
            FROM favoris f
            INNER JOIN annonces a ON f.id_anno = a.id_anno
            INNER JOIN categories c ON c.id_cat = a.id_cat
            LEFT JOIN photos p ON p.id_anno = a.id_anno AND p.ordre = 1
            WHERE f.id_user = $id_user_connecte
            ORDER BY f.created_at DESC";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 0) {
        echo '<p class="vide">Aucune annonce enregistrée dans vos favoris pour le moment.</p>';
    } else {
        while ($ligne = mysqli_fetch_assoc($result)) {
            $idAnno = (int) $ligne['id_anno'];
            $img = chemin_photo_affichage($ligne['chemin']);
            $desc = $ligne['description'];
            if (strlen($desc) > 100) { $desc = substr($desc, 0, 100) . '…'; }

            echo '<article class="carte">';
            echo '<a href="detail_annonce.php?id_anno=' . $idAnno . '">';
            if ($img !== '') { echo '<img src="' . $img . '" alt="">'; }
            echo '<h2>' . htmlspecialchars($ligne['titre']) . '</h2>';
            echo '<p class="prix">' . number_format((float) $ligne['prix'], 2, ',', ' ') . ' €</p>';
            echo '<p class="desc">' . htmlspecialchars($desc) . '</p>';
            echo '<p class="meta">' . htmlspecialchars($ligne['categorie']) . ' — ' . htmlspecialchars($ligne['localisation']) . '</p>';
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
