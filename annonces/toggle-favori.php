<?php
// F9 (Omar) — ajouter / retirer un favori
session_start();

if (!isset($_SESSION['id_user']) || !isset($_POST['id_anno'])) {
    header('Location: ../mon_compte/connexion.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$id_anno = (int) $_POST['id_anno'];

if ($id_anno <= 0) {
    header('Location: page_annonces.php');
    exit;
}

$conn = mysqli_connect('localhost', 'root', 'root', 'bd_blogmoteur');

if ($conn) {
    // Si le favori existe déjà → on le supprime ; sinon → on l'ajoute (bouton toggle)
    $check = mysqli_query(
        $conn,
        "SELECT id_fav FROM favoris WHERE id_user = $id_user AND id_anno = $id_anno LIMIT 1"
    );

    if ($check && mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "DELETE FROM favoris WHERE id_user = $id_user AND id_anno = $id_anno");
    } else {
        mysqli_query($conn, "INSERT INTO favoris (id_user, id_anno) VALUES ($id_user, $id_anno)");
    }

    mysqli_close($conn);
}

header('Location: detail_annonce.php?id_anno=' . $id_anno);
exit;
