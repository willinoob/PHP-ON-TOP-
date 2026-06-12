<?php
/**
 * Chemin d'affichage d'une photo — relatif (uploads/nom.jpg)
 * Fonctionne quel que soit le nom du dossier du projet sur le PC.
 */
function chemin_photo_affichage($chemin) {
    if (empty($chemin)) {
        return '';
    }

    $fichier = basename($chemin);
    if ($fichier === '') {
        return '';
    }

    return htmlspecialchars('uploads/' . rawurlencode($fichier), ENT_QUOTES, 'UTF-8');
}
