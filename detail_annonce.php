<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail annonce</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; background: #f5f5f0; max-width: 800px; margin: 0 auto; }
        a { color: #2a5bc4; }
        h1 { font-size: 1.5rem; }
        .prix { font-size: 1.2rem; font-weight: 600; color: #2a5bc4; }
        .photos img { max-width: 100%; max-height: 280px; margin: 0.5rem 0.5rem 0 0; border-radius: 8px; }
        dl { display: grid; grid-template-columns: 140px 1fr; gap: 0.35rem 1rem; background: #fff; padding: 1rem; border-radius: 10px; border: 1px solid #e0ddd5; }
        dt { color: #888; font-size: 0.85rem; }
        .description { background: #fff; padding: 1rem; border-radius: 10px; border: 1px solid #e0ddd5; margin-top: 1rem; }
        .erreur { color: #b91c1c; background: #fee2e2; padding: 1rem; border-radius: 8px; }
        .navigation a { font-weight: 600; }
        .favori-form { margin: 1rem 0; }
        .btn-favori { background: #2a5bc4; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-favori:hover { background: #1e4294; }
        .actions-annonce { display: flex; flex-wrap: wrap; gap: 0.75rem; margin: 1rem 0; align-items: center; }
        .btn-contact { display: inline-block; background: #fff; color: #2a5bc4; border: 2px solid #2a5bc4; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; text-decoration: none; }
        .btn-contact:hover { background: #2a5bc4; color: #fff; }
    </style>
</head>
<body>

<p class="navigation">
    <a href="page_annonces.php">&larr; Retour à la liste</a>
    | <a href="favoris.php">Mes favoris ⭐</a>
</p>

<?php
/**
 * F7 — Détail d'une annonce (Michè)
 * On lit id_anno dans l'URL (?id_anno=3), comme modifilm.php?idfilm=...
 */

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

// Vraie connexion (pour contact, nav, etc.)
$utilisateur_connecte = isset($_SESSION['id_user']);
$id_user_connecte = $utilisateur_connecte ? (int) $_SESSION['id_user'] : 0;

// F9 (Omar) : fallback test favoris si visiteur (Alice, id_user = 1) — sans modifier la session
$id_user_favori = $utilisateur_connecte ? $id_user_connecte : 1;

// 1) Récupérer l'identifiant depuis l'URL
$id_anno = isset($_GET['id_anno']) ? (int) $_GET['id_anno'] : 0;

if ($id_anno <= 0) {
    echo '<p class="erreur">Identifiant annonce invalide.</p>';
    echo '</body></html>';
    exit;
}

$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');

if (!$conn) {
    echo '<p class="erreur">Erreur connexion : ' . htmlspecialchars(mysqli_connect_error()) . '</p>';
    echo '</body></html>';
    exit;
}

// 2) Charger l'annonce + le nom de la catégorie
$sql = "SELECT a.*, c.nom AS categorie
        FROM annonces a
        INNER JOIN categories c ON c.id_cat = a.id_cat
        WHERE a.id_anno = " . $id_anno . "
        LIMIT 1";

$result = mysqli_query($conn, $sql);
$annonce = mysqli_fetch_assoc($result);

if (!$annonce) {
    echo '<p class="erreur">Annonce introuvable.</p>';
    mysqli_close($conn);
    echo '</body></html>';
    exit;
}

// 3) Bonus : compteur de vues (+1 seulement la 1ère visite de cette annonce dans la session)
if (!isset($_SESSION['annonces_vues'])) {
    $_SESSION['annonces_vues'] = [];
}

if (empty($_SESSION['annonces_vues'][$id_anno])) {
    mysqli_query($conn, 'UPDATE annonces SET vues = vues + 1 WHERE id_anno = ' . $id_anno);
    $annonce['vues'] = (int) $annonce['vues'] + 1;
    $_SESSION['annonces_vues'][$id_anno] = true;
}

// F9 (Omar) : cette annonce est-elle déjà en favori ?
$est_favori = false;
$res_fav = mysqli_query(
    $conn,
    'SELECT id_fav FROM favoris WHERE id_user = ' . $id_user_favori . ' AND id_anno = ' . $id_anno . ' LIMIT 1'
);
if ($res_fav && mysqli_fetch_assoc($res_fav)) {
    $est_favori = true;
}

// 4) Toutes les photos de cette annonce (tri par ordre)
$res_photos = mysqli_query($conn, 'SELECT chemin FROM photos WHERE id_anno = ' . $id_anno . ' ORDER BY ordre ASC');
?>

<h1><?= htmlspecialchars($annonce['titre']) ?></h1>
<p class="prix"><?= number_format((float) $annonce['prix'], 2, ',', ' ') ?> €</p>

<div class="actions-annonce">
    <form class="favori-form" method="POST" action="toggle-favori.php">
        <input type="hidden" name="id_anno" value="<?= $id_anno ?>">
        <button type="submit" class="btn-favori">
            <?= $est_favori ? '★ Retirer des favoris' : '☆ Ajouter aux favoris' ?>
        </button>
    </form>

    <?php if (!$utilisateur_connecte || $id_user_connecte !== (int) $annonce['id_user']): ?>
        <?php if ($utilisateur_connecte): ?>
            <a href="messages.php?id_anno=<?= $id_anno ?>" class="btn-contact">Contactez-nous</a>
        <?php else: ?>
            <a href="connexion.php" class="btn-contact">Contactez-nous</a>
        <?php endif; ?>
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

<!-- Caractéristiques du véhicule -->
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

<?php mysqli_close($conn); ?>

</body>
</html>
