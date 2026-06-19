<?php
/**
 * F2 — Connexion utilisateur (requête préparée + password_verify).
 */
session_start();

// Si on vient du bouton "Contactez-nous" sans être connecté, on garde la destination
$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '';
$id_anno_redirect = isset($_GET['id_anno']) ? (int) $_GET['id_anno'] : (int) ($_POST['id_anno'] ?? 0);
$succes_inscription = isset($_GET['inscription']) && $_GET['inscription'] === 'ok';

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
        $sql = 'SELECT id_user, nom, prenom, password, role_user FROM users WHERE email = ? LIMIT 1';
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt === false) {
            $erreur = 'Erreur technique. Veuillez réessayer.';
        } else {
            mysqli_stmt_bind_param($stmt, 's', $email); // 's' = le paramètre est une chaîne (string)
            mysqli_stmt_execute($stmt);
            $resultat = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($resultat);
            mysqli_stmt_close($stmt);

            // password_verify : compare le mdp saisi au hash stocké en base
            if ($user && password_verify($mdp, $user['password'])) {
                $_SESSION['id_user']   = (int) $user['id_user'];
                $_SESSION['prenom']    = $user['prenom'];
                $_SESSION['nom']       = $user['nom'];
                $_SESSION['role_user'] = (int) ($user['role_user'] ?? 0);

                if ($redirect === 'messages' && $id_anno_redirect > 0) {
                    header('Location: ../messagerie/messages.php?id_anno=' . $id_anno_redirect);
                } elseif ($redirect === 'favori' && $id_anno_redirect > 0) {
                    // L'annonce mise en favori avant connexion est ajoutée, puis on va dans l'espace favoris
                    $id_user_connecte = (int) $user['id_user'];
                    $deja = mysqli_query(
                        $conn,
                        "SELECT id_fav FROM favoris WHERE id_user = $id_user_connecte AND id_anno = $id_anno_redirect LIMIT 1"
                    );
                    if (!$deja || mysqli_num_rows($deja) === 0) {
                        mysqli_query(
                            $conn,
                            "INSERT INTO favoris (id_user, id_anno) VALUES ($id_user_connecte, $id_anno_redirect)"
                        );
                    }
                    header('Location: ../annonces/favoris.php');
                } else {
                    header('Location: ../annonces/page_annonces.php');
                }
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
    <?php include __DIR__ . '/../includes/favicon.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Le Blog Moteur</title>
    <link rel="stylesheet" href="../assets/css/base.css?v=<?= filemtime(__DIR__ . '/../assets/css/base.css') ?>">
    <link rel="stylesheet" href="../assets/css/connexion.css?v=<?= filemtime(__DIR__ . '/../assets/css/connexion.css') ?>">
</head>
<body class="page-connexion">

    <div class="box">

        <p class="page-connexion__logo">
            <img src="../assets/images/logo_blogmoteur.png" alt="Le Blog Moteur">
        </p>
        <hr>

        <form action="" method="post">

            <?php if ($redirect === 'messages' && $id_anno_redirect > 0): ?>
                <!-- Champs cachés : après connexion, on renvoie vers la messagerie de cette annonce -->
                <input type="hidden" name="redirect" value="messages">
                <input type="hidden" name="id_anno" value="<?= $id_anno_redirect ?>">
            <?php elseif ($redirect === 'favori' && $id_anno_redirect > 0): ?>
                <!-- Champs cachés : après connexion, l'annonce est ajoutée aux favoris -->
                <input type="hidden" name="redirect" value="favori">
                <input type="hidden" name="id_anno" value="<?= $id_anno_redirect ?>">
            <?php endif; ?>

            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="mdp" placeholder="Mot de passe" required><br><br>

            <?php if ($succes_inscription): ?>
                <p class="succes">Inscription réussie. Connectez-vous avec votre compte.</p>
            <?php endif; ?>

            <?php if ($erreur !== null): ?>
                <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>

            <input type="submit" value="Se connecter" name="connexion">

        </form>

        <p class="lien">Pas de compte ? <a href="inscription_blogmoteur.php">S'inscrire</a></p>

    </div>

</body>
</html>
