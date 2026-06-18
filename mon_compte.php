
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/site.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "root", "bd_blogmoteur");

if (!$conn) {
    die("Connexion BDD échouée");
}

$user_id = $_SESSION['user_id'];

/* ---------------- USER ---------------- */
$sqlUser = "SELECT * FROM users WHERE id_user = $user_id";
$userResult = mysqli_query($conn, $sqlUser);
$user = mysqli_fetch_assoc($userResult);

/* ---------------- ANNONCES ---------------- */
$sqlAnnonces = "SELECT COUNT(*) AS total FROM annonces WHERE id_user = $user_id";
$annonces = mysqli_fetch_assoc(mysqli_query($conn, $sqlAnnonces))['total'];

/* ---------------- FAVORIS ---------------- */
$sqlFavoris = "SELECT COUNT(*) AS total FROM favoris WHERE id_user = $user_id";
$favoris = mysqli_fetch_assoc(mysqli_query($conn, $sqlFavoris))['total'];

/* ---------------- MESSAGES ---------------- */
$sqlMessages = "SELECT COUNT(*) AS total FROM messages WHERE id_destinataire = $user_id";
$messages = mysqli_fetch_assoc(mysqli_query($conn, $sqlMessages))['total'];

/* ---------------- VUES TOTALES ---------------- */
$sqlVues = "SELECT SUM(vues) AS total FROM annonces WHERE id_user = $user_id";
$vues = mysqli_fetch_assoc(mysqli_query($conn, $sqlVues))['total'];
if (!$vues) $vues = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Profil</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#0b1220;
    color:white;
}

.container{
    padding:30px;
}

.header{
    background:#1e293b;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
}

.grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:15px;
}

.card{
    background:#334155;
    padding:20px;
    border-radius:12px;
    text-align:center;
    transition:0.3s;
    cursor:pointer;
}

.card:hover{
    transform:translateY(-5px);
    background:#475569;
}

a{
    color:white;
    text-decoration:none;
}
</style>

</head>
<body>

<div class="container">

    <div class="header">
        <h2>👤 <?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?></h2>
        <p><?= htmlspecialchars($user['email']) ?></p>
    </div>

    <div class="grid">

        <a href="mes_annonces.php">
            <div class="card">
                <h3>Annonces</h3>
                <p><?= $annonces ?></p>
            </div>
        </a>

        <a href="favoris.php">
            <div class="card">
                <h3>Favoris</h3>
                <p><?= $favoris ?></p>
            </div>
        </a>

        <a href="messages.php">
            <div class="card">
                <h3>Messages</h3>
                <p><?= $messages ?></p>
            </div>
        </a>

        <div class="card">
            <h3>Vues totales</h3>
            <p><?= $vues ?></p>
        </div>

    </div>

</div>

</body>
</html>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
