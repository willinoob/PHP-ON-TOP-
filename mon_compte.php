
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


if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "root", "bd_blogmoteur");

if (!$conn) {
    die("Connexion BDD échouée");
}

$id_user = $_SESSION['id_user'];

/* ---------------- USER ---------------- */
$sqlUser = "SELECT * FROM users WHERE id_user = $id_user";
$userResult = mysqli_query($conn, $sqlUser);
$user = mysqli_fetch_assoc($userResult);

/* ---------------- ANNONCES ---------------- */
$sqlAnnonces = "SELECT COUNT(*) AS total FROM annonces WHERE id_user = $id_user";
$annonces = mysqli_fetch_assoc(mysqli_query($conn, $sqlAnnonces))['total'];

/* ---------------- FAVORIS ---------------- */
$sqlFavoris = "SELECT COUNT(*) AS total FROM favoris WHERE id_user = $id_user";
$favoris = mysqli_fetch_assoc(mysqli_query($conn, $sqlFavoris))['total'];

/* ---------------- MESSAGES ---------------- */
$sqlMessages = "SELECT COUNT(*) AS total FROM messages WHERE id_destinataire = $id_user";
$messages = mysqli_fetch_assoc(mysqli_query($conn, $sqlMessages))['total'];

/* ---------------- VUES TOTALES ---------------- */
$sqlVues = "SELECT SUM(vues) AS total FROM annonces WHERE id_user = $id_user";
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
    <div class="header">

    <h2>👤 <?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?></h2>
    <p><?= htmlspecialchars($user['email']) ?></p>

    <br>
<div style="background:#0f172a; padding:20px; border-radius:12px; margin-top:15px; border:1px solid #334155;">

    <h3 style="color:#f1f5f9; margin-bottom:15px; letter-spacing:1px;">
        INFORMATIONS PERSONNELLES
    </h3>

    <div style="display:flex; flex-direction:column; gap:8px; color:#cbd5e1; font-size:15px;">

        <div>
            <strong style="color:#94a3b8;">Nom :</strong>
            <?= htmlspecialchars($user['nom']) ?>
        </div>

        <div>
            <strong style="color:#94a3b8;">Prénom :</strong>
            <?= htmlspecialchars($user['prenom']) ?>
        </div>
    </div>

</div>
    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px;">

        <a href="modifier_profil.php" style="padding:10px; background:#3b82f6; border-radius:8px; text-align:center;">
            Modifier le profil
        </a>

        <a href="changer_mot_de_passe.php" style="padding:10px; background:#f59e0b; border-radius:8px; text-align:center;">
            Changer le mot de passe
        </a>

        <a href="logout.php" style="padding:10px; background:#ef4444; border-radius:8px; text-align:center;">
            Déconnexion
        </a>

    </div>

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
