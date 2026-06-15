<?php

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

function chemin_photo_vignette($chemin) {
    if (empty($chemin)) {
        return '';
    }

    $point = strrpos($chemin, '.');
    if ($point === false) {
        return chemin_photo_affichage($chemin);
    }

    $chemin_vignette = substr($chemin, 0, $point) . '_thumb' . substr($chemin, $point);
    $fichier_vignette = dirname(__DIR__) . '/uploads/' . basename($chemin_vignette);

    if (is_file($fichier_vignette)) {
        return chemin_photo_affichage($chemin_vignette);
    }

    return chemin_photo_affichage($chemin);
}
