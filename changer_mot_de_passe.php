<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$message = '';

$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');
if (!$conn) {
    die('Erreur de connexion : ' . mysqli_connect_error());
}

if (isset($_POST['update_password'])) {
    $ancien = $_POST['ancien_mdp'] ?? '';
    $nouveau = $_POST['nouveau_mdp'] ?? '';
    $confirmer = $_POST['confirmer_mdp'] ?? '';

    if ($nouveau !== $confirmer) {
        $message = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($nouveau) < 10) {
        $message = 'Le nouveau mot de passe doit contenir au moins 10 caractères.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT password FROM users WHERE id_user = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user && password_verify($ancien, $user['password'])) {
            $hash = password_hash($nouveau, PASSWORD_DEFAULT);
            $stmtUpdate = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE id_user = ?');
            mysqli_stmt_bind_param($stmtUpdate, 'si', $hash, $id_user);
            mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);
            $message = 'Mot de passe mis à jour avec succès.';
        } else {
            $message = 'Ancien mot de passe incorrect.';
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/site.css">
</head>
<body class="page-connexion">

    <div class="box">
        <p class="page-connexion__logo">
            <img src="assets/images/logo_blogmoteur.png" alt="Le Blog Moteur">
        </p>
        <hr>

        <h1 class="page-connexion__titre">Changer le mot de passe</h1>

        <?php if ($message !== ''): ?>
            <p class="<?= str_contains($message, 'succès') ? 'succes' : 'erreur' ?>"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="password" name="ancien_mdp" placeholder="Ancien mot de passe" required><br><br>
            <input type="password" name="nouveau_mdp" placeholder="Nouveau mot de passe" required><br><br>
            <input type="password" name="confirmer_mdp" placeholder="Confirmer le mot de passe" required><br><br>
            <input type="submit" name="update_password" value="Modifier">
        </form>

        <p class="lien"><a href="mon_compte.php">← Retour à mon compte</a></p>
    </div>

</body>
</html>
