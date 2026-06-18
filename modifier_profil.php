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

/* ---------------- UPDATE ---------------- */
if (isset($_POST['update'])) {

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    $sqlUpdate = "UPDATE users 
                  SET nom='$nom', prenom='$prenom', email='$email' 
                  WHERE id_user=$id_user";

    mysqli_query($conn, $sqlUpdate);

    header("Location: mon_compte.php");
    exit();
}

/* ---------------- SELECT USER ---------------- */
$sql = "SELECT * FROM users WHERE id_user = $id_user";
$user = mysqli_fetch_assoc(mysqli_query($conn, $sql));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier profil</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#0b1220;
    color:white;
}

.container{
    max-width:500px;
    margin:50px auto;
    background:#1e293b;
    padding:20px;
    border-radius:12px;
}

h2{
    text-align:center;
    margin-bottom:20px;
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
    background:#3b82f6;
    color:white;
    cursor:pointer;
}

button:hover{
    background:#2563eb;
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

    <h2>Modifier mon profil</h2>

    <form method="POST">

        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

        <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>

        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <button type="submit" name="update">Enregistrer</button>

    </form>

    <a href="mon_compte.php">← Retour</a>

</div>

</body>
</html>