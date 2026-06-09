<?php
// Omar — F9 : ajouter / retirer un favori (Traitement natif PHP)
session_start();

if (!isset($_SESSION['id_user']) || !isset($_POST['id_anno'])) {
    // Si problème ou accès direct interdit, retour à l'accueil
    header('Location: page_annonces.php');
    exit;
}

$id_user = (int)$_SESSION['id_user'];
$id_anno = (int)$_POST['id_anno'];

$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');

if ($conn) {
    // Vérification de l'existence du favori
    $check = mysqli_query($conn, "SELECT id_fav FROM favoris WHERE id_user = $id_user AND id_anno = $id_anno");

    if (mysqli_num_rows($check) > 0) {
        // Déjà en favori -> Suppression
        mysqli_query($conn, "DELETE FROM favoris WHERE id_user = $id_user AND id_anno = $id_anno");
    } else {
        // Pas encore en favori -> Ajout
        mysqli_query($conn, "INSERT INTO favoris (id_user, id_anno) VALUES ($id_user, $id_anno)");
    }
    
    mysqli_close($conn);
}

// Redirection immédiate vers la page de détail pour simuler un changement d'état fluide
header('Location: detail_annonce.php?id_anno=' . $id_anno);
exit;