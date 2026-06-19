<?php
/**
 * Modifier nom, prénom, email du compte connecté.
 */
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$erreur = null;

$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');
if (!$conn) {
    $erreur = 'Erreur de connexion : ' . mysqli_connect_error();
}

if ($conn && isset($_POST['update'])) {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nom === '' || $prenom === '' || $email === '') {
        $erreur = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Email invalide.';
    } else {
        $stmt = mysqli_prepare($conn, 'UPDATE users SET nom = ?, prenom = ?, email = ? WHERE id_user = ?');
        mysqli_stmt_bind_param($stmt, 'sssi', $nom, $prenom, $email, $id_user); // sss = 3 textes, i = id
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;

        header('Location: mon_compte.php');
        exit;
    }
}

$user = null;
if ($conn && $erreur === null) {
    $res = mysqli_query($conn, 'SELECT nom, prenom, email FROM users WHERE id_user = ' . $id_user);
    $user = mysqli_fetch_assoc($res);
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include __DIR__ . '/includes/favicon.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le profil — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/base.css?v=<?= filemtime(__DIR__ . '/assets/css/base.css') ?>">
    <link rel="stylesheet" href="assets/css/modifier_profil.css?v=<?= filemtime(__DIR__ . '/assets/css/modifier_profil.css') ?>">
</head>
<body class="page-connexion">

    <div class="box">
        <p class="page-connexion__logo">
            <img src="assets/images/logo_blogmoteur.png" alt="Le Blog Moteur">
        </p>
        <hr>

        <h1 class="page-connexion__titre">Modifier mon profil</h1>

        <?php if ($erreur !== null): ?>
            <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="nom" placeholder="Nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required><br><br>
            <input type="text" name="prenom" placeholder="Prénom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required><br><br>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required><br><br>
            <input type="submit" name="update" value="Enregistrer">
        </form>

        <p class="lien"><a href="mon_compte.php">← Retour à mon compte</a></p>
    </div>

</body>
</html>
