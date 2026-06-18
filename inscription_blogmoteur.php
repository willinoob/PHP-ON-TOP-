<?php
session_start();
$erreurs = [];
$id = mysqli_connect("localhost", "root", "root", "bd_blogmoteur");
if (isset($_POST['inscrire'])) {
    $nom    = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email  = $_POST['email'];
    $mdp    = $_POST['mdp'];
    $confirmation_mdp = $_POST['confirmation_mdp'];
    if (isset($_POST['url'])) {
        $url = $_POST['url'];
    } else {
        $url = '';
    }
    if (!empty($url)) {
        $erreurs['url'] = " ";
    }

    if (empty($nom)) {
        $erreurs['nom'] = "Le nom est obligatoire.";
    }
    if (empty($prenom)) {
        $erreurs['prenom'] = "Le prénom est obligatoire.";
    }
    if (empty($mdp)) {
        $erreurs['mdp'] = "Le mot de passe est obligatoire.";
    }
    if (strlen($mdp) < 10) {
        $erreurs['mdp'] = "Le mot de passe doit contenir au moins 10 caractères.";
    }
    if ($mdp !== $confirmation_mdp) {
        $erreurs['confirmation_mdp'] = "Le mot de passe ne correspond pas.";
    }
    if (empty($email)) {
        $erreurs['email'] = "L'email est obligatoire.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = "L'email n'est pas valide.";
    }
    if (empty($erreurs)) {
        $requete_preparée_email = mysqli_prepare($id, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($requete_preparée_email, "s", $email);
        mysqli_stmt_execute($requete_preparée_email);
        $resultat = mysqli_stmt_get_result($requete_preparée_email);

        if (mysqli_num_rows($resultat) > 0) {
            $erreurs['email'] = "Cet email est déjà utilisé.";
        }

        if (empty($erreurs)) {
            $mdp_hache = password_hash($mdp, PASSWORD_DEFAULT);
            $requete_champs = mysqli_prepare($id, "INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($requete_champs, "ssss", $nom, $prenom, $email, $mdp_hache);
            mysqli_stmt_execute($requete_champs);


            echo "Inscription réussie !<br><br>";
            echo "<a href='connexion.php'>Aller à la connexion</a><br>";
            echo "<a href='accueil.php'>Aller à la page d'accueil</a>";
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/site.css">
    <title>Inscription — Le Blog Moteur</title>
</head>

<body class="page-connexion">
    <div class="box box--inscription">
        <p class="page-connexion__logo">
            <img src="assets/images/logo_blogmoteur.png" alt="Le Blog Moteur">
        </p>
        <hr>
        <form action="" method="post">
            <input type="text" name="nom" placeholder="Nom :" value="<?php echo htmlspecialchars($nom ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
            <?php if (isset($erreurs['nom'])) {
                echo '<p class="erreur">' . htmlspecialchars($erreurs['nom']) . '</p>';
            } ?>
            <input type="text" name="prenom" placeholder="Prénom :" value="<?php echo htmlspecialchars($prenom ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
            <?php if (isset($erreurs['prenom'])) {
                echo '<p class="erreur">' . htmlspecialchars($erreurs['prenom']) . '</p>';
            } ?>
            <input type="email" name="email" placeholder="Email :" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
            <?php if (isset($erreurs['email'])) {
                echo '<p class="erreur">' . htmlspecialchars($erreurs['email']) . '</p>';
            } ?>
            <input type="password" name="mdp" placeholder="Mot de passe :" required><br><br>
            <?php if (isset($erreurs['mdp'])) {
                echo '<p class="erreur">' . htmlspecialchars($erreurs['mdp']) . '</p>';
            } ?>
            <input type="password" name="confirmation_mdp" placeholder="Confirmer le Mot de passe :" required><br><br>
            <?php if (isset($erreurs['confirmation_mdp'])) {
                echo '<p class="erreur">' . htmlspecialchars($erreurs['confirmation_mdp']) . '</p>';
            } ?>
            <div class="oeuf-de-paques"><input type="text" name="url" tabindex="-1" autocomplete="off"></div>

            <input type="submit" value="S'inscrire" name="inscrire">
        </form>
        <p class="lien">Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
    </div>
</body>

</html>