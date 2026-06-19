<?php
/**
 * Upload photos — redimensionnement, compression JPEG, création de vignette _thumb.
 * Utilisé par formulaire_creation_annonces.php et formulaire_modif_annonces.php.
 */

const PHOTO_LARGEUR_MAX = 1600;
const PHOTO_HAUTEUR_MAX = 1200;
const PHOTO_VIGNETTE_LARGEUR = 400;
const PHOTO_VIGNETTE_HAUTEUR = 300;
const PHOTO_QUALITE_JPEG = 82;
const PHOTO_QUALITE_VIGNETTE = 78;
const PHOTO_TAILLE_MAX_OCTETS = 5242880; // 5 Mo

/**
 * Enregistre une photo uploadée : redimensionnement, compression JPEG + vignette.
 *
 * @return array{ok: bool, nom_fichier?: string, chemin_web?: string, erreur?: string}
 */
function enregistrer_photo_upload(string $tmp_path, string $dossier_upload, string $nom_original): array
{
    if (!extension_loaded('gd')) {
        return ['ok' => false, 'erreur' => 'Le serveur ne peut pas traiter les images (GD manquant).'];
    }

    if (!is_uploaded_file($tmp_path)) {
        // Sécurité : le fichier doit venir d'un vrai upload HTTP, pas d'un chemin local bidon
        return ['ok' => false, 'erreur' => 'Fichier uploadé invalide.'];
    }

    if (!is_dir($dossier_upload) && !mkdir($dossier_upload, 0755, true)) {
        return ['ok' => false, 'erreur' => 'Impossible de créer le dossier uploads.'];
    }

    $taille = filesize($tmp_path);
    if ($taille === false || $taille > PHOTO_TAILLE_MAX_OCTETS) {
        return ['ok' => false, 'erreur' => 'La photo est trop lourde (maximum 5 Mo).'];
    }

    $source = charger_image_depuis_fichier($tmp_path);
    if ($source === null) {
        return ['ok' => false, 'erreur' => 'Format de photo non autorisé (jpg, png, webp).'];
    }

    $image = redimensionner_image($source['ressource'], $source['largeur'], $source['hauteur'], PHOTO_LARGEUR_MAX, PHOTO_HAUTEUR_MAX);

    $base = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($nom_original, PATHINFO_FILENAME));
    // Nom unique : timestamp + nom nettoyé (évite les espaces/accents dans uploads/)
    $nom_fichier = time() . '_' . $base . '.jpg';
    $chemin_complet = rtrim($dossier_upload, '/\\') . DIRECTORY_SEPARATOR . $nom_fichier;

    if (!imagejpeg($image, $chemin_complet, PHOTO_QUALITE_JPEG)) {
        imagedestroy($image);
        return ['ok' => false, 'erreur' => 'Impossible d\'enregistrer la photo optimisée.'];
    }

    $chemin_vignette = chemin_fichier_vignette($chemin_complet);
    enregistrer_vignette($image, $chemin_vignette);

    imagedestroy($image);

    return [
        'ok' => true,
        'nom_fichier' => $nom_fichier,
        // Chemin relatif : fonctionne quel que soit le nom du dossier du projet
        'chemin_web' => 'uploads/' . $nom_fichier,
    ];
}

/**
 * Optimise une image déjà présente sur le disque (script de migration).
 *
 * @return array{ok: bool, erreur?: string}
 */
function optimiser_image_existante(string $chemin_fichier): array
{
    if (!extension_loaded('gd')) {
        return ['ok' => false, 'erreur' => 'GD manquant.'];
    }

    if (!is_file($chemin_fichier)) {
        return ['ok' => false, 'erreur' => 'Fichier introuvable.'];
    }

    if (str_contains(basename($chemin_fichier), '_thumb.')) {
        return ['ok' => false, 'erreur' => 'Vignette ignorée.'];
    }

    $source = charger_image_depuis_fichier($chemin_fichier);
    if ($source === null) {
        return ['ok' => false, 'erreur' => 'Format non supporté.'];
    }

    $image = redimensionner_image($source['ressource'], $source['largeur'], $source['hauteur'], PHOTO_LARGEUR_MAX, PHOTO_HAUTEUR_MAX);

    $extension = strtolower(pathinfo($chemin_fichier, PATHINFO_EXTENSION));
    $enregistre = match ($extension) {
        'jpg', 'jpeg' => imagejpeg($image, $chemin_fichier, PHOTO_QUALITE_JPEG),
        'png'         => imagepng($image, $chemin_fichier, 6),
        'webp'        => imagewebp($image, $chemin_fichier, PHOTO_QUALITE_JPEG),
        default       => false,
    };

    if (!$enregistre) {
        imagedestroy($image);
        return ['ok' => false, 'erreur' => 'Échec de l\'enregistrement.'];
    }

    enregistrer_vignette($image, chemin_fichier_vignette($chemin_fichier));
    imagedestroy($image);

    return ['ok' => true];
}

/**
 * @return array{ressource: \GdImage, largeur: int, hauteur: int}|null
 */
function charger_image_depuis_fichier(string $chemin): ?array
{
    $info = @getimagesize($chemin);
    if ($info === false) {
        return null;
    }

    $ressource = match ($info['mime']) {
        'image/jpeg' => @imagecreatefromjpeg($chemin),
        'image/png'  => @imagecreatefrompng($chemin),
        'image/webp' => @imagecreatefromwebp($chemin),
        default      => false,
    };

    if ($ressource === false) {
        return null;
    }

    return [
        'ressource' => $ressource,
        'largeur'   => (int) $info[0],
        'hauteur'   => (int) $info[1],
    ];
}

function redimensionner_image(\GdImage $source, int $largeur, int $hauteur, int $max_largeur, int $max_hauteur): \GdImage
{
    // ratio : si l'image est déjà petite, on ne l'agrandit pas (min(..., 1))
    $ratio = min($max_largeur / $largeur, $max_hauteur / $hauteur, 1);
    $nouvelle_largeur = max(1, (int) round($largeur * $ratio));
    $nouvelle_hauteur = max(1, (int) round($hauteur * $ratio));

    if ($ratio >= 1) {
        return $source;
    }

    $destination = imagecreatetruecolor($nouvelle_largeur, $nouvelle_hauteur);
    // imagecopyresampled : redimensionne proprement (anti pixelisation)
    imagecopyresampled(
        $destination,
        $source,
        0,
        0,
        0,
        0,
        $nouvelle_largeur,
        $nouvelle_hauteur,
        $largeur,
        $hauteur
    );
    imagedestroy($source);

    return $destination;
}

function enregistrer_vignette(\GdImage $source, string $chemin_vignette): void
{
    $largeur = imagesx($source);
    $hauteur = imagesy($source);
    $copie = imagecreatetruecolor($largeur, $hauteur);
    imagecopy($copie, $source, 0, 0, 0, 0, $largeur, $hauteur);

    $vignette = redimensionner_image(
        $copie,
        $largeur,
        $hauteur,
        PHOTO_VIGNETTE_LARGEUR,
        PHOTO_VIGNETTE_HAUTEUR
    );

    imagejpeg($vignette, $chemin_vignette, PHOTO_QUALITE_VIGNETTE);
    imagedestroy($vignette);
}

function chemin_fichier_vignette(string $chemin_fichier): string
{
    // photo.jpg → photo_thumb.jpg (même dossier)
    $extension = pathinfo($chemin_fichier, PATHINFO_EXTENSION);

    if ($extension === '') {
        return $chemin_fichier . '_thumb';
    }

    $sans_extension = substr($chemin_fichier, 0, -strlen($extension) - 1);

    return $sans_extension . '_thumb.' . $extension;
}

function supprimer_photos_annonce(string $chemin_web): void
{
    if ($chemin_web === '') {
        return;
    }

    $dossier_upload = dirname(__DIR__) . '/uploads/';
    $nom = basename($chemin_web);
    $fichier = $dossier_upload . $nom;

    if (is_file($fichier)) {
        unlink($fichier);
    }

    $vignette = chemin_fichier_vignette($fichier);
    if (is_file($vignette)) {
        unlink($vignette);
    }
}
