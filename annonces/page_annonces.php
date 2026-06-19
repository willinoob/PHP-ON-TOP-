<?php

session_start();

require_once __DIR__ . '/../includes/photos.php';

// 1) Connexion MySQL (XAMPP : user root, mot de passe vide)
$conn = mysqli_connect('localhost', 'root', 'root', 'bd_blogmoteur');
$sql_injection = '';

// L'utilisateur connecté voit aussi ses propres annonces dans la liste publique,
// avec un badge + les boutons Modifier / Supprimer (pour voir le rendu côté client).
$id_user_connecte = isset($_SESSION['id_user']) ? (int) $_SESSION['id_user'] : 0;

if ($conn) {
    // F8 (Omar) : construction des conditions SQL à partir des filtres GET
    require __DIR__ . '/filtres-where.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include __DIR__ . '/../includes/favicon.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des annonces — Le Blog Moteur</title>
    <link rel="stylesheet" href="../assets/css/base.css?v=<?= filemtime(__DIR__ . '/../assets/css/base.css') ?>">
    <link rel="stylesheet" href="../assets/css/page_annonces.css?v=<?= filemtime(__DIR__ . '/../assets/css/page_annonces.css') ?>">
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="site-main">
    <h1>Liste des annonces</h1>

    <?php if ($conn) { include __DIR__ . '/form-filtres.php'; } ?>

    <div class="grille">
<?php
if (!$conn) {
    echo '<p class="erreur">Erreur de connexion MySQL. Vérifiez que MySQL est démarré dans XAMPP (bouton vert), puis rechargez la page.</p>';
} else {
    // LEFT JOIN photos : on récupère la 1ère photo (ordre = 1), même si l'annonce n'en a pas
    $sql = "SELECT a.id_anno, a.id_user, a.titre, a.prix, a.description, a.localisation,
                   c.nom AS categorie, p.chemin
            FROM annonces a
            INNER JOIN categories c ON c.id_cat = a.id_cat
            LEFT JOIN photos p ON p.id_anno = a.id_anno AND p.ordre = 1
            WHERE 1=1" . $sql_injection . "
            ORDER BY a.created_at DESC";
    // $sql_injection est construit dans filtres-where.php à partir des filtres GET

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo '<p class="erreur">Erreur SQL : ' . htmlspecialchars(mysqli_error($conn)) . '</p>';
    } elseif (mysqli_num_rows($result) === 0) {
        echo '<p class="vide">Aucune annonce trouvée.</p>';
    } else {
        // 3) Une ligne SQL = une carte
        while ($ligne = mysqli_fetch_assoc($result)) {
            $idAnno = (int) $ligne['id_anno'];
            $img = chemin_photo_vignette($ligne['chemin']); // version _thumb si elle existe

            // true si l'annonce appartient à l'utilisateur connecté
            $est_proprietaire = $id_user_connecte > 0 && (int) $ligne['id_user'] === $id_user_connecte;

            // Description courte sur la carte (pas tout le texte)
            $desc = $ligne['description'];
            if (strlen($desc) > 100) {
                $desc = substr($desc, 0, 100) . '…';
            }

            echo '<article class="carte' . ($est_proprietaire ? ' carte--proprietaire' : '') . '">';
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
            echo '</a>';

            // Boutons Modifier / Supprimer uniquement sur ses propres annonces
            if ($est_proprietaire) {
                echo '<div class="carte__actions">';
                echo '<a href="formulaire_modif_annonces.php?id_anno=' . $idAnno . '" class="btn-valider">Modifier</a>';
                echo '<form method="post" action="supprimer_annonce.php" class="carte__form-supprimer"'
                    . ' onsubmit="return confirm(\'Supprimer cette annonce ? Cette action est irréversible.\');">';
                echo '<input type="hidden" name="id_anno" value="' . $idAnno . '">';
                echo '<button type="submit" class="btn-supprimer">Supprimer</button>';
                echo '</form>';
                echo '</div>';
            }

            echo '</article>';
        }
    }

    mysqli_close($conn);
}
?>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
