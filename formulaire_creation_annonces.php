<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');

$erreur = null;
$succes = false;

// Valeurs du formulaire (pour réafficher en cas d'erreur)
$valeurs = [
    'id_cat'        => '',
    'titre'         => '',
    'prix'          => '',
    'description'   => '',
    'marque'        => '',
    'modele'        => '',
    'annee'         => '',
    'kilometrage'   => '',
    'carburant'     => '',
    'boite_vitesse' => '',
    'etat'          => '',
    'localisation'  => '',
    'telephone'     => '',
];

$categories = [];

if ($conn) {
    $res_cats = mysqli_query($conn, 'SELECT id_cat, nom FROM categories ORDER BY nom ASC');
    while ($cat = mysqli_fetch_assoc($res_cats)) {
        $categories[] = $cat;
    }
}

if ($conn && isset($_POST['valider'])) {

    foreach (array_keys($valeurs) as $champ) {
        $valeurs[$champ] = trim($_POST[$champ] ?? '');
    }

    $id_cat      = (int) $valeurs['id_cat'];
    $prix        = (float) str_replace(',', '.', $valeurs['prix']);
    $annee       = (int) $valeurs['annee'];
    $kilometrage = (int) $valeurs['kilometrage'];

    if ($id_cat <= 0 || $valeurs['titre'] === '' || $valeurs['description'] === ''
        || $valeurs['marque'] === '' || $valeurs['modele'] === ''
        || $annee <= 0 || $kilometrage < 0 || $prix <= 0
        || $valeurs['carburant'] === '' || $valeurs['boite_vitesse'] === ''
        || $valeurs['etat'] === '' || $valeurs['localisation'] === '') {
        $erreur = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (empty($_FILES['photo']['name'])) {
        $erreur = 'Veuillez sélectionner une photo.';
    } elseif ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $erreur = 'Erreur lors de l\'envoi de la photo.';
    } else {
        $extensions_ok = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $extensions_ok, true)) {
            $erreur = 'Format de photo non autorisé (jpg, png, webp).';
        } else {
            $sql = 'INSERT INTO annonces (
                        id_user, id_cat, titre, prix, description,
                        marque, modele, annee, kilometrage,
                        carburant, boite_vitesse, etat, localisation, telephone
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt === false) {
                $erreur = 'Erreur technique. Veuillez réessayer.';
            } else {
                $telephone = $valeurs['telephone'];

                mysqli_stmt_bind_param(
                    $stmt,
                    'iisdsssiissss',
                    $id_user,
                    $id_cat,
                    $valeurs['titre'],
                    $prix,
                    $valeurs['description'],
                    $valeurs['marque'],
                    $valeurs['modele'],
                    $annee,
                    $kilometrage,
                    $valeurs['carburant'],
                    $valeurs['boite_vitesse'],
                    $valeurs['etat'],
                    $valeurs['localisation'],
                    $telephone
                );

                if (!mysqli_stmt_execute($stmt)) {
                    $erreur = 'Impossible d\'enregistrer l\'annonce.';
                } else {
                    $id_anno = (int) mysqli_insert_id($conn);
                    mysqli_stmt_close($stmt);

                    $nom_fichier = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['photo']['name']));
                    $dossier_upload = __DIR__ . '/uploads/';

                    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $dossier_upload . $nom_fichier)) {
                        mysqli_query($conn, 'DELETE FROM annonces WHERE id_anno = ' . $id_anno);
                        $erreur = 'Impossible d\'enregistrer la photo sur le serveur.';
                    } else {
                        $chemin = '/blogmoteur_annonces/uploads/' . $nom_fichier;

                        $sql_photo = 'INSERT INTO photos (id_anno, chemin, ordre) VALUES (?, ?, 1)';
                        $stmt_photo = mysqli_prepare($conn, $sql_photo);

                        if ($stmt_photo) {
                            mysqli_stmt_bind_param($stmt_photo, 'is', $id_anno, $chemin);
                            mysqli_stmt_execute($stmt_photo);
                            mysqli_stmt_close($stmt_photo);
                        }

                        header('Location: detail_annonce.php?id_anno=' . $id_anno);
                        exit;
                    }
                }
            }
        }
    }
}

if ($conn) {
    mysqli_close($conn);
}

function champ_val($valeurs, $nom) {
    return htmlspecialchars($valeurs[$nom] ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déposer une annonce — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/site.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="site-main">
    <h1>Déposer une annonce</h1>

    <div class="form-annonce">
        <form action="" method="post" enctype="multipart/form-data" class="form-annonce__inner">

            <?php if ($erreur !== null): ?>
                <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>

            <div class="form-annonce__groupe">
                <label for="id_cat">Catégorie *</label>
                <select name="id_cat" id="id_cat" required>
                    <option value="">Choisir une catégorie</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int) $cat['id_cat'] ?>" <?= $valeurs['id_cat'] === (string) $cat['id_cat'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-annonce__groupe">
                <label for="titre">Titre de l'annonce *</label>
                <input type="text" name="titre" id="titre" maxlength="150" required
                       placeholder="Ex : Peugeot 208 — très bon état"
                       value="<?= champ_val($valeurs, 'titre') ?>">
            </div>

            <div class="form-annonce__ligne">
                <div class="form-annonce__groupe">
                    <label for="prix">Prix (€) *</label>
                    <input type="number" name="prix" id="prix" min="0" step="0.01" required
                           placeholder="Ex : 9500"
                           value="<?= champ_val($valeurs, 'prix') ?>">
                </div>
                <div class="form-annonce__groupe">
                    <label for="annee">Année *</label>
                    <input type="number" name="annee" id="annee" min="1990" max="2099" required
                           placeholder="Ex : 2019"
                           value="<?= champ_val($valeurs, 'annee') ?>">
                </div>
            </div>

            <div class="form-annonce__groupe">
                <label for="description">Description *</label>
                <textarea name="description" id="description" rows="4" required
                          placeholder="Décrivez le véhicule, son état, l'entretien…"><?= champ_val($valeurs, 'description') ?></textarea>
            </div>

            <div class="form-annonce__ligne">
                <div class="form-annonce__groupe">
                    <label for="marque">Marque *</label>
                    <input type="text" name="marque" id="marque" maxlength="50" required
                           placeholder="Ex : Peugeot"
                           value="<?= champ_val($valeurs, 'marque') ?>">
                </div>
                <div class="form-annonce__groupe">
                    <label for="modele">Modèle *</label>
                    <input type="text" name="modele" id="modele" maxlength="50" required
                           placeholder="Ex : 208"
                           value="<?= champ_val($valeurs, 'modele') ?>">
                </div>
            </div>

            <div class="form-annonce__ligne">
                <div class="form-annonce__groupe">
                    <label for="kilometrage">Kilométrage *</label>
                    <input type="number" name="kilometrage" id="kilometrage" min="0" required
                           placeholder="Ex : 62000"
                           value="<?= champ_val($valeurs, 'kilometrage') ?>">
                </div>
                <div class="form-annonce__groupe">
                    <label for="carburant">Carburant *</label>
                    <select name="carburant" id="carburant" required>
                        <option value="">Choisir</option>
                        <option value="essence" <?= $valeurs['carburant'] === 'essence' ? 'selected' : '' ?>>Essence</option>
                        <option value="diesel" <?= $valeurs['carburant'] === 'diesel' ? 'selected' : '' ?>>Diesel</option>
                        <option value="electrique" <?= $valeurs['carburant'] === 'electrique' ? 'selected' : '' ?>>Électrique</option>
                    </select>
                </div>
            </div>

            <div class="form-annonce__ligne">
                <div class="form-annonce__groupe">
                    <label for="boite_vitesse">Boîte de vitesse *</label>
                    <select name="boite_vitesse" id="boite_vitesse" required>
                        <option value="">Choisir</option>
                        <option value="manuelle" <?= $valeurs['boite_vitesse'] === 'manuelle' ? 'selected' : '' ?>>Manuelle</option>
                        <option value="automatique" <?= $valeurs['boite_vitesse'] === 'automatique' ? 'selected' : '' ?>>Automatique</option>
                    </select>
                </div>
                <div class="form-annonce__groupe">
                    <label for="etat">État *</label>
                    <select name="etat" id="etat" required>
                        <option value="">Choisir</option>
                        <option value="occasion" <?= $valeurs['etat'] === 'occasion' ? 'selected' : '' ?>>Occasion</option>
                        <option value="neuf" <?= $valeurs['etat'] === 'neuf' ? 'selected' : '' ?>>Neuf</option>
                    </select>
                </div>
            </div>

            <div class="form-annonce__ligne">
                <div class="form-annonce__groupe">
                    <label for="localisation">Localisation *</label>
                    <input type="text" name="localisation" id="localisation" maxlength="100" required
                           placeholder="Ex : 75011 Paris"
                           value="<?= champ_val($valeurs, 'localisation') ?>">
                </div>
                <div class="form-annonce__groupe">
                    <label for="telephone">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" maxlength="20"
                           placeholder="Ex : 0600000000"
                           value="<?= champ_val($valeurs, 'telephone') ?>">
                </div>
            </div>

            <div class="form-annonce__groupe">
                <label for="photo">Photo du véhicule *</label>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp" required>
                <small class="form-annonce__aide">Choisissez une image sur votre ordinateur (jpg, png, webp).</small>
            </div>

            <div class="form-annonce__actions">
                <button type="submit" name="valider" class="btn-valider">Valider</button>
                <a href="page_annonces.php" class="btn-annuler">Annuler</a>
            </div>

        </form>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
