<?php
// F9 (Omar) — ajouter / retirer un favori
session_start();

if (!isset($_SESSION['id_user']) || !isset($_POST['id_anno'])) {
    header('Location: connexion.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$id_anno = (int) $_POST['id_anno'];

if ($id_anno <= 0) {
    header('Location: page_annonces.php');
    exit;
}

$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');

if ($conn) {
    $id_user_sql = $id_user;
    $id_anno_sql = $id_anno;

    $check = mysqli_query(
        $conn,
        "SELECT id_fav FROM favoris WHERE id_user = $id_user_sql AND id_anno = $id_anno_sql LIMIT 1"
    );

    if ($check && mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "DELETE FROM favoris WHERE id_user = $id_user_sql AND id_anno = $id_anno_sql");
    } else {
        mysqli_query($conn, "INSERT INTO favoris (id_user, id_anno) VALUES ($id_user_sql, $id_anno_sql)");
    }

    mysqli_close($conn);
}

header('Location: detail_annonce.php?id_anno=' . $id_anno);
exit;
