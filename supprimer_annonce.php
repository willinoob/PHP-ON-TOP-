<?php
/**
 * Suppression d'une annonce — uniquement si elle appartient à l'utilisateur connecté.
 */
session_start();

if (!isset($_SESSION['id_user']) || !isset($_POST['id_anno'])) {
    header('Location: connexion.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$id_anno = (int) $_POST['id_anno'];

if ($id_anno <= 0) {
    header('Location: mes_annonces.php');
    exit;
}

require_once __DIR__ . '/includes/traitement_image.php';

$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');

if ($conn) {
    // Sécurité : on ne supprime que si l'annonce est bien à nous
    $check = mysqli_query(
        $conn,
        'SELECT id_anno FROM annonces WHERE id_anno = ' . $id_anno . ' AND id_user = ' . $id_user . ' LIMIT 1'
    );

        if ($check && mysqli_num_rows($check) > 0) {
            // Supprimer les fichiers photo sur le disque avant la ligne en base
            $resPhotos = mysqli_query($conn, 'SELECT chemin FROM photos WHERE id_anno = ' . $id_anno);
        if ($resPhotos) {
            while ($photo = mysqli_fetch_assoc($resPhotos)) {
                supprimer_photos_annonce($photo['chemin']);
            }
        }

        mysqli_query($conn, 'DELETE FROM annonces WHERE id_anno = ' . $id_anno . ' AND id_user = ' . $id_user);
    }

    mysqli_close($conn);
}

header('Location: mes_annonces.php');
exit;
