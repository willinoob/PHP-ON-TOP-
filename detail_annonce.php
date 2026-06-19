<?php
/**
 * F7 — Fiche détail : galerie photos, favori, contact, compteur de vues.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/photos.php';

$utilisateur_connecte = isset($_SESSION['id_user']);
$id_user_connecte = $utilisateur_connecte ? (int) $_SESSION['id_user'] : 0;

$id_anno = isset($_GET['id_anno']) ? (int) $_GET['id_anno'] : 0;
$erreur = null;
$annonce = null;
$est_favori = false;
$est_proprietaire = false;
$photos = [];

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
            // Compteur de vues : +1 par annonce et par session (pas à chaque F5)
            if (!isset($_SESSION['annonces_vues'])) {
                $_SESSION['annonces_vues'] = [];
            }

            if (empty($_SESSION['annonces_vues'][$id_anno])) {
                mysqli_query($conn, 'UPDATE annonces SET vues = vues + 1 WHERE id_anno = ' . $id_anno);
                $annonce['vues'] = (int) $annonce['vues'] + 1;
                $_SESSION['annonces_vues'][$id_anno] = true;
            }

            // true si l'annonce appartient à l'utilisateur connecté → on masque favori / contact
            $est_proprietaire = $utilisateur_connecte && (int) $annonce['id_user'] === $id_user_connecte;

            if ($utilisateur_connecte && !$est_proprietaire) {
                $res_fav = mysqli_query(
                    $conn,
                    'SELECT id_fav FROM favoris WHERE id_user = ' . $id_user_connecte . ' AND id_anno = ' . $id_anno . ' LIMIT 1'
                );
                if ($res_fav && mysqli_fetch_assoc($res_fav)) {
                    $est_favori = true;
                }
            }

            $photos = [];
            $res_photos = mysqli_query($conn, 'SELECT chemin FROM photos WHERE id_anno = ' . $id_anno . ' ORDER BY ordre ASC');
            if ($res_photos) {
                while ($photo = mysqli_fetch_assoc($res_photos)) {
                    $photos[] = $photo['chemin'];
                }
            }
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
    <?php include __DIR__ . '/includes/favicon.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_titre ?></title>
    <link rel="stylesheet" href="assets/css/base.css?v=<?= filemtime(__DIR__ . '/assets/css/base.css') ?>">
    <link rel="stylesheet" href="assets/css/detail_annonce.css?v=<?= filemtime(__DIR__ . '/assets/css/detail_annonce.css') ?>">
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

            <div class="detail-entete">
                <h1><?= htmlspecialchars($annonce['titre']) ?></h1>
                <p class="prix"><?= number_format((float) $annonce['prix'], 2, ',', ' ') ?> €</p>
            </div>

            <div class="actions-annonce">
                <?php if ($est_proprietaire): ?>
                    <a href="formulaire_modif_annonces.php?id_anno=<?= $id_anno ?>" class="btn-modifier">Modifier</a>
                    <form method="post" action="supprimer_annonce.php" class="supprimer-form"
                          onsubmit="return confirm('Supprimer cette annonce ? Cette action est irréversible.');">
                        <input type="hidden" name="id_anno" value="<?= $id_anno ?>">
                        <button type="submit" class="btn-supprimer">Supprimer</button>
                    </form>
                <?php else: ?>
                    <?php if ($utilisateur_connecte): ?>
                    <form class="favori-form" method="post" action="toggle-favori.php">
                        <input type="hidden" name="id_anno" value="<?= $id_anno ?>">
                        <button type="submit" class="btn-favori">
                            <?= $est_favori ? '★ Retirer des favoris' : '☆ Ajouter aux favoris' ?>
                        </button>
                    </form>
                    <a href="fenetre_messages.php?id_anno=<?= $id_anno ?>" class="btn-contact">Contactez-nous</a>
                    <?php else: ?>
                    <a href="connexion.php?redirect=favori&amp;id_anno=<?= $id_anno ?>" class="btn-favori">☆ Ajouter aux favoris</a>
                    <a href="connexion.php?redirect=messages&amp;id_anno=<?= $id_anno ?>" class="btn-contact">Contactez-nous</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if (count($photos) === 0): ?>
                <div class="detail-galerie detail-galerie--vide">
                    <p>Aucune photo.</p>
                </div>
            <?php else: ?>
                <div class="detail-galerie">
                    <div class="detail-galerie__principale">
                        <img id="detail-photo-principale"
                             src="<?= chemin_photo_affichage($photos[0]) ?>"
                             alt="Photo de <?= htmlspecialchars($annonce['titre']) ?>">
                    </div>
                    <?php if (count($photos) > 1): ?>
                    <div class="detail-galerie__vignettes" role="tablist" aria-label="Photos de l'annonce">
                        <?php foreach ($photos as $i => $chemin): ?>
                        <button type="button"
                                class="detail-galerie__vignette<?= $i === 0 ? ' is-active' : '' ?>"
                                data-src="<?= chemin_photo_affichage($chemin) ?>"
                                aria-label="Afficher la photo <?= $i + 1 ?>">
                            <img src="<?= chemin_photo_affichage($chemin) ?>" alt="">
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

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

<?php if (!empty($photos) && count($photos) > 1): ?>
<script>
// Clic sur une vignette → change la grande photo + surligne la vignette active
document.querySelectorAll('.detail-galerie__vignette').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var main = document.getElementById('detail-photo-principale');
        if (!main) return;
        main.src = btn.getAttribute('data-src');
        document.querySelectorAll('.detail-galerie__vignette').forEach(function (b) {
            b.classList.remove('is-active');
        });
        btn.classList.add('is-active');
    });
});
</script>
<?php endif; ?>

</body>
</html>
