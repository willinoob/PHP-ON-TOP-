<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function chemin_photo_affichage($chemin) {
    if (empty($chemin)) {
        return '';
    }
    $pos = strrpos($chemin, '/');
    if ($pos === false) {
        return htmlspecialchars(rawurlencode($chemin));
    }
    $dossier = substr($chemin, 0, $pos + 1);
    $fichier = substr($chemin, $pos + 1);
    return htmlspecialchars($dossier . rawurlencode($fichier));
}

$utilisateur_connecte = isset($_SESSION['id_user']);
$id_user_connecte = $utilisateur_connecte ? (int) $_SESSION['id_user'] : 0;

// F9 (Omar) : fallback test favoris si visiteur (Alice, id_user = 1) — sans modifier la session
$id_user_favori = $utilisateur_connecte ? $id_user_connecte : 1;

$id_anno = isset($_GET['id_anno']) ? (int) $_GET['id_anno'] : 0;
$erreur = null;
$annonce = null;
$est_favori = false;
$res_photos = null;

if ($id_anno <= 0) {
    $erreur = 'Identifiant annonce invalide.';
} else {
    $conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');

    if (!$conn) {
        $erreur = 'Erreur connexion : ' . mysqli_connect_error();
    } else {
        $sql = "SELECT a.*, c.nom AS categorie
                FROM annonces a
                INNER JOIN categories c ON c.id_cat = a.id_cat
                WHERE a.id_anno = " . $id_anno . "
                LIMIT 1";

        $result = mysqli_query($conn, $sql);
        $annonce = mysqli_fetch_assoc($result);

        if (!$annonce) {
            $erreur = 'Annonce introuvable.';
        } else {
            if (!isset($_SESSION['annonces_vues'])) {
                $_SESSION['annonces_vues'] = [];
            }

            if (empty($_SESSION['annonces_vues'][$id_anno])) {
                mysqli_query($conn, 'UPDATE annonces SET vues = vues + 1 WHERE id_anno = ' . $id_anno);
                $annonce['vues'] = (int) $annonce['vues'] + 1;
                $_SESSION['annonces_vues'][$id_anno] = true;
            }

            $res_fav = mysqli_query(
                $conn,
                'SELECT id_fav FROM favoris WHERE id_user = ' . $id_user_favori . ' AND id_anno = ' . $id_anno . ' LIMIT 1'
            );
            if ($res_fav && mysqli_fetch_assoc($res_fav)) {
                $est_favori = true;
            }

            $res_photos = mysqli_query($conn, 'SELECT chemin FROM photos WHERE id_anno = ' . $id_anno . ' ORDER BY ordre ASC');
        }

        mysqli_close($conn);
    }
}

$page_titre = $annonce
    ? htmlspecialchars($annonce['titre']) . ' — Le Blog Moteur'
    : 'Détail annonce — Le Blog Moteur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_titre ?></title>
    <link rel="stylesheet" href="assets/css/site.css">
    <style>
        /* Styles fiche détail — inchangés, contenu centré à 800px */
        .detail-contenu { max-width: 800px; margin: 0 auto; font-family: sans-serif; }
        .detail-contenu a { color: #c87941; }
        .detail-contenu h1 { font-size: 1.5rem; margin: 0 0 0.25rem; color: #1a2d4a; }
        .detail-contenu .prix { font-size: 1.2rem; font-weight: 600; color: #c87941; }
        .detail-contenu .photos img { max-width: 100%; max-height: 280px; margin: 0.5rem 0.5rem 0 0; border-radius: 8px; border: 1px solid #d4dce8; }
        .detail-contenu dl { display: grid; grid-template-columns: 140px 1fr; gap: 0.35rem 1rem; background: #fff; padding: 1rem; border-radius: 10px; border: 1px solid #d4dce8; color: #1a2d4a; }
        .detail-contenu dt { color: #5a6b7d; font-size: 0.85rem; }
        .detail-contenu .description { background: #fff; padding: 1rem; border-radius: 10px; border: 1px solid #d4dce8; margin-top: 1rem; color: #1a2d4a; }
        .detail-contenu .navigation { margin: 0 0 1rem; }
        .detail-contenu .navigation a { font-weight: 600; text-decoration: none; }
        .detail-contenu .navigation a:hover { text-decoration: underline; }
        .detail-contenu .favori-form { margin: 1rem 0; }
        .detail-contenu .btn-favori { background: #c87941; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .detail-contenu .btn-favori:hover { background: #a6632f; }
        .detail-contenu .actions-annonce { display: flex; flex-wrap: wrap; gap: 0.75rem; margin: 1rem 0; align-items: center; }
        .detail-contenu .btn-contact { display: inline-block; background: #fff; color: #c87941; border: 2px solid #c87941; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; text-decoration: none; }
        .detail-contenu .btn-contact:hover { background: #c87941; color: #fff; }
    </style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="site-main">
    <div class="detail-contenu">

        <p class="navigation">
            <a href="page_annonces.php">&larr; Retour à la liste</a>
        </p>

        <?php if ($erreur !== null): ?>
            <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php else: ?>

            <h1><?= htmlspecialchars($annonce['titre']) ?></h1>
            <p class="prix"><?= number_format((float) $annonce['prix'], 2, ',', ' ') ?> €</p>

            <div class="actions-annonce">
                <?php if ($utilisateur_connecte): ?>
                <form class="favori-form" method="post" action="toggle-favori.php">
                    <input type="hidden" name="id_anno" value="<?= $id_anno ?>">
                    <button type="submit" class="btn-favori">
                        <?= $est_favori ? '★ Retirer des favoris' : '☆ Ajouter aux favoris' ?>
                    </button>
                </form>
                <?php endif; ?>

                <?php if (!$utilisateur_connecte): ?>
                    <a href="connexion.php?redirect=messages&amp;id_anno=<?= $id_anno ?>" class="btn-contact">Contactez-nous</a>
                <?php endif; ?>
            </div>

            <div class="photos">
                <?php
                $nb_photos = 0;
                while ($photo = mysqli_fetch_assoc($res_photos)) {
                    $nb_photos++;
                    $src = chemin_photo_affichage($photo['chemin']);
                    echo '<img src="' . $src . '" alt="Photo annonce">';
                }
                if ($nb_photos === 0) {
                    echo '<p>Aucune photo.</p>';
                }
                ?>
            </div>

            <dl>
                <dt>Catégorie</dt><dd><?= htmlspecialchars($annonce['categorie']) ?></dd>
                <dt>Marque / Modèle</dt><dd><?= htmlspecialchars($annonce['marque'] . ' ' . $annonce['modele']) ?></dd>
                <dt>Année</dt><dd><?= (int) $annonce['annee'] ?></dd>
                <dt>Kilométrage</dt><dd><?= (int) $annonce['kilometrage'] ?> km</dd>
                <dt>Carburant</dt><dd><?= htmlspecialchars($annonce['carburant']) ?></dd>
                <dt>Boîte</dt><dd><?= htmlspecialchars($annonce['boite_vitesse']) ?></dd>
                <dt>État</dt><dd><?= htmlspecialchars($annonce['etat']) ?></dd>
                <?php if (!empty($annonce['couleur'])): ?>
                <dt>Couleur</dt><dd><?= htmlspecialchars($annonce['couleur']) ?></dd>
                <?php endif; ?>
                <dt>Localisation</dt><dd><?= htmlspecialchars($annonce['localisation']) ?></dd>
                <?php if (!empty($annonce['telephone'])): ?>
                <dt>Téléphone</dt><dd><?= htmlspecialchars($annonce['telephone']) ?></dd>
                <?php endif; ?>
                <dt>Vues</dt><dd><?= (int) $annonce['vues'] ?></dd>
            </dl>

            <section class="description">
                <h2>Description</h2>
                <p><?= nl2br(htmlspecialchars($annonce['description'])) ?></p>
            </section>

        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
