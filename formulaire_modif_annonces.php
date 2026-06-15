<?php
// Démarrage de la session pour vérifier que l'utilisateur est connecté
session_start();

require_once __DIR__ . '/includes/traitement_image.php';

// Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];

// Récupération de l'id de l'annonce depuis l'URL (ex: modifier_annonce.php?id_anno=5)
if (!isset($_GET['id_anno']) || (int) $_GET['id_anno'] <= 0) {
    // Si pas d'id valide dans l'URL, on redirige vers la liste des annonces
    header('Location: page_annonces.php');
    exit;
}

$id_anno = (int) $_GET['id_anno'];

// Connexion à la base de données
$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');

$erreur = null;
$succes = false;
$annonce = null;
$categories = [];

if ($conn) {
    // --- Chargement des catégories pour le select 
    $res_cats = mysqli_query($conn, 'SELECT id_cat, nom FROM categories ORDER BY nom ASC');
    while ($cat = mysqli_fetch_assoc($res_cats)) {
        $categories[] = $cat;
    }

    // --- Chargement de l'annonce existante ---
    // On vérifie aussi que l'annonce appartient bien à l'utilisateur connecté (sécurité)
    $sql_select = 'SELECT * FROM annonces WHERE id_anno = ? AND id_user = ?';
    $stmt_select = mysqli_prepare($conn, $sql_select);
    mysqli_stmt_bind_param($stmt_select, 'ii', $id_anno, $id_user);
    mysqli_stmt_execute($stmt_select);
    $res = mysqli_stmt_get_result($stmt_select);
    $annonce = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt_select);

    // Si l'annonce n'existe pas ou n'appartient pas à l'utilisateur, on redirige
    if (!$annonce) {
        header('Location: page_annonces.php');
        exit;
    }

    // Récupération de la photo actuelle de l'annonce (la première photo, ordre 1)
    $sql_photo = 'SELECT chemin FROM photos WHERE id_anno = ? ORDER BY ordre ASC LIMIT 1';
    $stmt_photo = mysqli_prepare($conn, $sql_photo);
    mysqli_stmt_bind_param($stmt_photo, 'i', $id_anno);
    mysqli_stmt_execute($stmt_photo);
    $res_photo = mysqli_stmt_get_result($stmt_photo);
    $photo_actuelle = mysqli_fetch_assoc($res_photo);
    mysqli_stmt_close($stmt_photo);

    // --- Traitement du formulaire de modification ---
    if (isset($_POST['valider'])) {

        // Récupération et nettoyage de tous les champs du formulaire
        $id_cat      = (int) ($_POST['id_cat'] ?? 0);
        $titre       = trim($_POST['titre'] ?? '');
        $prix        = (float) str_replace(',', '.', trim($_POST['prix'] ?? ''));
        $description = trim($_POST['description'] ?? '');
        $marque      = trim($_POST['marque'] ?? '');
        $modele      = trim($_POST['modele'] ?? '');
        $annee       = (int) ($_POST['annee'] ?? 0);
        $kilometrage = (int) ($_POST['kilometrage'] ?? 0);
        $carburant   = trim($_POST['carburant'] ?? '');
        $boite       = trim($_POST['boite_vitesse'] ?? '');
        $etat        = trim($_POST['etat'] ?? '');
        $localisation = trim($_POST['localisation'] ?? '');
        $telephone   = trim($_POST['telephone'] ?? '');

        // Validation des champs obligatoires
        if ($id_cat <= 0 || $titre === '' || $description === ''
            || $marque === '' || $modele === ''
            || $annee <= 0 || $kilometrage < 0 || $prix <= 0
            || $carburant === '' || $boite === ''
            || $etat === '' || $localisation === '') {
            $erreur = 'Veuillez remplir tous les champs obligatoires.';
        } else {

            // --- Mise à jour de l'annonce en base ---
            $sql_update = 'UPDATE annonces SET
                            id_cat = ?, titre = ?, prix = ?, description = ?,
                            marque = ?, modele = ?, annee = ?, kilometrage = ?,
                            carburant = ?, boite_vitesse = ?, etat = ?,
                            localisation = ?, telephone = ?
                           WHERE id_anno = ? AND id_user = ?';

            $stmt_update = mysqli_prepare($conn, $sql_update);

            if ($stmt_update === false) {
                $erreur = 'Erreur technique. Veuillez réessayer.';
            } else {
                mysqli_stmt_bind_param(
                    $stmt_update,
                    'isdsssiisssssii',
                    $id_cat,
                    $titre,
                    $prix,
                    $description,
                    $marque,
                    $modele,
                    $annee,
                    $kilometrage,
                    $carburant,
                    $boite,
                    $etat,
                    $localisation,
                    $telephone,
                    $id_anno,
                    $id_user
                );

                if (!mysqli_stmt_execute($stmt_update)) {
                    $erreur = 'Impossible de modifier l\'annonce.';
                } else {
                    mysqli_stmt_close($stmt_update);

                    // --- Gestion de la photo (optionnelle à la modification) ---
                    // On ne remplace la photo que si l'utilisateur en a choisi une nouvelle
                    if (!empty($_FILES['photo']['name'])) {

                        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                            $erreur = 'Erreur lors de l\'envoi de la photo.';
                        } else {
                            $extensions_ok = ['jpg', 'jpeg', 'png', 'webp'];
                            $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

                            if (!in_array($extension, $extensions_ok, true)) {
                                $erreur = 'Format de photo non autorisé (jpg, png, webp).';
                            } else {
                                if ($photo_actuelle && !empty($photo_actuelle['chemin'])) {
                                    supprimer_photos_annonce($photo_actuelle['chemin']);
                                }

                                $resultat_photo = enregistrer_photo_upload(
                                    $_FILES['photo']['tmp_name'],
                                    __DIR__ . '/uploads/',
                                    $_FILES['photo']['name']
                                );

                                if (!$resultat_photo['ok']) {
                                    $erreur = $resultat_photo['erreur'];
                                } else {
                                    $nouveau_chemin = $resultat_photo['chemin_web'];

                                    // On met à jour le chemin de la photo en base (ordre 1 = photo principale)
                                    $sql_update_photo = 'UPDATE photos SET chemin = ? WHERE id_anno = ? AND ordre = 1';
                                    $stmt_up_photo = mysqli_prepare($conn, $sql_update_photo);

                                    if ($stmt_up_photo) {
                                        mysqli_stmt_bind_param($stmt_up_photo, 'si', $nouveau_chemin, $id_anno);
                                        mysqli_stmt_execute($stmt_up_photo);
                                        mysqli_stmt_close($stmt_up_photo);
                                    }
                                }
                            }
                        }
                    }

                    // Si pas d'erreur sur la photo, on redirige vers le détail de l'annonce
                    if ($erreur === null) {
                        header('Location: detail_annonce.php?id_anno=' . $id_anno);
                        exit;
                    }
                }
            }
        }

        // En cas d'erreur, on remet les valeurs postées dans $annonce pour réafficher le formulaire
        $annonce['id_cat']        = $id_cat;
        $annonce['titre']         = $titre;
        $annonce['prix']          = $prix;
        $annonce['description']   = $description;
        $annonce['marque']        = $marque;
        $annonce['modele']        = $modele;
        $annonce['annee']         = $annee;
        $annonce['kilometrage']   = $kilometrage;
        $annonce['carburant']     = $carburant;
        $annonce['boite_vitesse'] = $boite;
        $annonce['etat']          = $etat;
        $annonce['localisation']  = $localisation;
        $annonce['telephone']     = $telephone;
    }

    mysqli_close($conn);
}

// Fonction utilitaire pour afficher une valeur dans un champ HTML en sécurisant
function val($annonce, $champ) {
    return htmlspecialchars($annonce[$champ] ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'annonce — Le Blog Moteur</title>
    <!-- Feuille de style principale du site -->
    <link rel="stylesheet" href="assets/css/site.css">
    <!-- Feuille de style spécifique à ce formulaire -->
    <link rel="stylesheet" href="style_formulaire_modif.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="site-main">

    <div class="form-modif">

        <h1 class="form-modif__titre">Modifier l'annonce</h1>

        <form action="" method="post" enctype="multipart/form-data" class="form-modif__inner">

            <!-- Message d'erreur affiché si un champ est invalide -->
            <?php if ($erreur !== null): ?>
                <p class="form-modif__erreur"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>

            <!-- Champ : Nom de l'annonce (titre) -->
            <div class="form-modif__groupe">
                <label for="titre">Nom de l'annonce *</label>
                <input type="text" name="titre" id="titre" maxlength="150" required
                       value="<?= val($annonce, 'titre') ?>">
            </div>

            <!-- Ligne : Prix / Catégorie / Ville (localisation) -->
            <div class="form-modif__ligne trois-colonnes">

                <div class="form-modif__groupe">
                    <label for="prix">Prix (€) *</label>
                    <input type="number" name="prix" id="prix" min="0" step="0.01" required
                           value="<?= val($annonce, 'prix') ?>">
                </div>

                <div class="form-modif__groupe">
                    <label for="id_cat">Catégorie *</label>
                    <select name="id_cat" id="id_cat" required>
                        <option value="">Choisir</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int) $cat['id_cat'] ?>"
                                <?= (string) $annonce['id_cat'] === (string) $cat['id_cat'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-modif__groupe">
                    <label for="localisation">Ville *</label>
                    <input type="text" name="localisation" id="localisation" maxlength="100" required
                           value="<?= val($annonce, 'localisation') ?>">
                </div>

            </div>

            <!-- Champ : Description -->
            <div class="form-modif__groupe">
                <label for="description">Description *</label>
                <textarea name="description" id="description" rows="5" required><?= val($annonce, 'description') ?></textarea>
            </div>

            <!-- Ligne : Marque / Année / Kilométrage -->
            <div class="form-modif__ligne trois-colonnes">

                <div class="form-modif__groupe">
                    <label for="marque">Marque</label>
                    <input type="text" name="marque" id="marque" maxlength="50"
                           value="<?= val($annonce, 'marque') ?>">
                </div>

                <div class="form-modif__groupe">
                    <label for="annee">Année</label>
                    <input type="number" name="annee" id="annee" min="1990" max="2099"
                           value="<?= val($annonce, 'annee') ?>">
                </div>

                <div class="form-modif__groupe">
                    <label for="kilometrage">Kilométrage</label>
                    <input type="number" name="kilometrage" id="kilometrage" min="0"
                           value="<?= val($annonce, 'kilometrage') ?>">
                </div>

            </div>

            <!-- Ligne : Modèle / Carburant / Boîte de vitesse -->
            <div class="form-modif__ligne trois-colonnes">

                <div class="form-modif__groupe">
                    <label for="modele">Modèle *</label>
                    <input type="text" name="modele" id="modele" maxlength="50" required
                           value="<?= val($annonce, 'modele') ?>">
                </div>

                <div class="form-modif__groupe">
                    <label for="carburant">Carburant *</label>
                    <select name="carburant" id="carburant" required>
                        <option value="">Choisir</option>
                        <option value="essence"    <?= $annonce['carburant'] === 'essence'    ? 'selected' : '' ?>>Essence</option>
                        <option value="diesel"     <?= $annonce['carburant'] === 'diesel'     ? 'selected' : '' ?>>Diesel</option>
                        <option value="electrique" <?= $annonce['carburant'] === 'electrique' ? 'selected' : '' ?>>Électrique</option>
                    </select>
                </div>

                <div class="form-modif__groupe">
                    <label for="boite_vitesse">Boîte de vitesse *</label>
                    <select name="boite_vitesse" id="boite_vitesse" required>
                        <option value="">Choisir</option>
                        <option value="manuelle"    <?= $annonce['boite_vitesse'] === 'manuelle'    ? 'selected' : '' ?>>Manuelle</option>
                        <option value="automatique" <?= $annonce['boite_vitesse'] === 'automatique' ? 'selected' : '' ?>>Automatique</option>
                    </select>
                </div>

            </div>

            <!-- Ligne : État / Téléphone -->
            <div class="form-modif__ligne">

                <div class="form-modif__groupe">
                    <label for="etat">État *</label>
                    <select name="etat" id="etat" required>
                        <option value="">Choisir</option>
                        <option value="occasion" <?= $annonce['etat'] === 'occasion' ? 'selected' : '' ?>>Occasion</option>
                        <option value="neuf"     <?= $annonce['etat'] === 'neuf'     ? 'selected' : '' ?>>Neuf</option>
                    </select>
                </div>

                <div class="form-modif__groupe">
                    <label for="telephone">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" maxlength="20"
                           value="<?= val($annonce, 'telephone') ?>">
                </div>

            </div>

            <!-- Photo actuelle de l'annonce -->
            <div class="form-modif__groupe">
                <label>Photo actuelle</label>
                <?php if ($photo_actuelle && $photo_actuelle['chemin']): ?>
                    <img src="<?= htmlspecialchars($photo_actuelle['chemin']) ?>"
                         alt="Photo actuelle de l'annonce"
                         class="form-modif__photo-actuelle">
                <?php else: ?>
                    <p class="form-modif__pas-photo">Aucune photo enregistrée.</p>
                <?php endif; ?>
            </div>

            <!-- Champ pour remplacer la photo (optionnel) -->
            <div class="form-modif__groupe">
                <label for="photo">Remplacer la photo</label>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp">
                <small class="form-modif__aide">Laissez vide pour conserver la photo actuelle. Max 5 Mo — optimisée automatiquement.</small>
            </div>

            <!-- Boutons d'action -->
            <div class="form-modif__actions">
                <button type="submit" name="valider" class="btn-valider">Enregistrer</button>
                <a href="detail_annonce.php?id_anno=<?= $id_anno ?>" class="btn-annuler">Annuler</a>
            </div>

        </form>
    </div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>