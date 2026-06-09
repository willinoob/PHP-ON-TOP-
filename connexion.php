<?php
session_start();

$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');
$erreur = null;

if (!$conn) {
    $erreur = 'Impossible de se connecter à la base de données.';
}

if ($conn && isset($_POST['connexion'])) {

    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mdp'] ?? '';

    if ($email === '' || $mdp === '') {
        $erreur = 'Email ou mot de passe incorrect.';
    } else {
        // Requête préparée : l'email n'est jamais collé dans la chaîne SQL (anti-injection)
        $sql = 'SELECT id_user, nom, prenom, password FROM users WHERE email = ? LIMIT 1';
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt === false) {
            $erreur = 'Erreur technique. Veuillez réessayer.';
        } else {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $resultat = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($resultat);
            mysqli_stmt_close($stmt);

            // password_verify : compare le mdp saisi au hash stocké en base
            if ($user && password_verify($mdp, $user['password'])) {
                $_SESSION['id_user'] = (int) $user['id_user'];
                $_SESSION['prenom']  = $user['prenom'];
                $_SESSION['nom']     = $user['nom'];
                header('Location: page_annonces.php');
                exit;
            }

            $erreur = 'Email ou mot de passe incorrect.';
        }
    }
}

if ($conn) {
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/site.css">
</head>
<body class="page-connexion">

    <div class="box">

        <h1>Le Blog Moteur</h1>
        <hr>

        <form action="" method="post">

            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="mdp" placeholder="Mot de passe" required><br><br>

            <?php if ($erreur !== null): ?>
                <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>

            <input type="submit" value="Se connecter" name="connexion">

        </form>

        <p class="lien">Pas de compte ? <a href="inscription_blogmoteur.php">S'inscrire</a></p>

    </div>

</body>
</html>
