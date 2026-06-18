<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "root", "bd_blogmoteur");
if (!$conn) {
    die("Connexion BDD échouée");
}

$id_user = $_SESSION['id_user'];
$message = "";

/* ---------------- UPDATE PASSWORD ---------------- */
if (isset($_POST['update_password'])) {

    $ancien = $_POST['ancien_mdp'];
    $nouveau = $_POST['nouveau_mdp'];
    $confirmer = $_POST['confirmer_mdp'];

    if ($nouveau !== $confirmer) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {

        // récupérer le mot de passe actuel hashé
        $sql = "SELECT password FROM users WHERE id_user=$id_user";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);

        // vérifier ancien mot de passe
        if (password_verify($ancien, $user['password'])) {

            // hash du nouveau mot de passe
            $hash = password_hash($nouveau, PASSWORD_DEFAULT);

            $update = "UPDATE users 
                       SET password='$hash' 
                       WHERE id_user=$id_user";

            mysqli_query($conn, $update);

            $message = "Mot de passe mis à jour avec succès.";
        } else {
            $message = "Ancien mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Changer mot de passe</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#0b1220;
    color:white;
}

.container{
    max-width:500px;
    margin:60px auto;
    background:#1e293b;
    padding:20px;
    border-radius:12px;
}

input{
    width:100%;
    padding:12px;
    margin:8px 0;
    border:none;
    border-radius:8px;
    background:#334155;
    color:white;
}

button{
    width:100%;
    padding:12px;
    margin-top:15px;
    border:none;
    border-radius:8px;
    background:#f59e0b;
    color:white;
    cursor:pointer;
}

button:hover{
    background:#d97706;
}

.msg{
    text-align:center;
    margin-bottom:10px;
    color:#93c5fd;
}
a{
    display:block;
    text-align:center;
    margin-top:10px;
    color:#94a3b8;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="container">

    <h2>Changer mot de passe</h2>

    <?php if ($message != ""): ?>
        <div class="msg"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="password" name="ancien_mdp" placeholder="Ancien mot de passe" required>

        <input type="password" name="nouveau_mdp" placeholder="Nouveau mot de passe" required>

        <input type="password" name="confirmer_mdp" placeholder="Confirmer mot de passe" required>

        <button type="submit" name="update_password">Modifier</button>

    </form>

    <a href="mon_compte.php">← Retour</a>

</div>

</body>
</html>