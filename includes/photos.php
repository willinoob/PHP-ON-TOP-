<?php
/**
 * Helpers pour afficher les photos dans les listes et le détail.
 * Gère les noms de fichiers avec espaces/accents (encodage URL).
 */

/**
 * Transforme "uploads/Ma Photo.jpg" en URL valide pour le navigateur.
 */
function chemin_photo_affichage($chemin) {
    if (empty($chemin)) {
        return '';
    }
    // '../' : les pages sont dans des sous-dossiers (annonces/, mon_compte/...),
    // le dossier uploads/ est resté à la racine du projet.
    $pos = strrpos($chemin, '/');
    if ($pos === false) {
        return '../' . htmlspecialchars(rawurlencode($chemin));
    }
    $dossier = substr($chemin, 0, $pos + 1);
    $fichier = substr($chemin, $pos + 1);
    // rawurlencode sur le nom du fichier seulement (les / restent des séparateurs)
    return '../' . htmlspecialchars($dossier . rawurlencode($fichier));
}

/**
 * Essaie d'utiliser la vignette _thumb.jpg ; sinon retombe sur la photo normale.
 */
function chemin_photo_vignette($chemin) {
    if (empty($chemin)) {
        return '';
    }

    $point = strrpos($chemin, '.');
    if ($point === false) {
        return chemin_photo_affichage($chemin);
    }

    // Ex : photo.jpg → photo_thumb.jpg
    $chemin_vignette = substr($chemin, 0, $point) . '_thumb' . substr($chemin, $point);
    $fichier_vignette = dirname(__DIR__) . '/uploads/' . basename($chemin_vignette);

    if (is_file($fichier_vignette)) {
        return chemin_photo_affichage($chemin_vignette);
    }

    return chemin_photo_affichage($chemin);
}
