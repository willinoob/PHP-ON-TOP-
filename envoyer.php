<?php
// Messagerie (William) — traitement envoi d'un message
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

$id_expediteur = (int) $_SESSION['id_user'];
$id_destinataire = (int) ($_POST['id_destinataire'] ?? 0);
$id_anno = (int) ($_POST['id_anno'] ?? 0);
$contenu = trim($_POST['contenu'] ?? '');

if ($contenu === '' || $id_anno <= 0 || $id_destinataire <= 0 || $id_expediteur === $id_destinataire) {
    header('Location: messages.php');
    exit;
}

$conn = mysqli_connect('localhost', 'root', 'root', 'bd_blogmoteur');

if ($conn) {
    $sql = 'INSERT INTO messages (id_anno, id_expediteur, id_destinataire, contenu) VALUES (?, ?, ?, ?)';
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'iiis', $id_anno, $id_expediteur, $id_destinataire, $contenu);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
}

header('Location: messages.php?id_anno=' . $id_anno . '&id_destinataire=' . $id_destinataire);
exit;
