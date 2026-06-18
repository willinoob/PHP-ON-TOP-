<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];

$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');
$erreur = null;
$user = null;
$annonces = 0;
$favoris = 0;
$messages = 0;
$vues = 0;

if (!$conn) {
    $erreur = 'Erreur de connexion : ' . mysqli_connect_error();
} else {
    $sqlUser = 'SELECT * FROM users WHERE id_user = ' . $id_user;
    $userResult = mysqli_query($conn, $sqlUser);

    if (!$userResult) {
        $erreur = 'Erreur SQL : ' . mysqli_error($conn);
    } else {
        $user = mysqli_fetch_assoc($userResult);

        if (!$user) {
            $erreur = 'Utilisateur introuvable.';
        } else {
            $annonces = (int) mysqli_fetch_assoc(mysqli_query(
                $conn,
                'SELECT COUNT(*) AS total FROM annonces WHERE id_user = ' . $id_user
            ))['total'];

            $favoris = (int) mysqli_fetch_assoc(mysqli_query(
                $conn,
                'SELECT COUNT(*) AS total FROM favoris WHERE id_user = ' . $id_user
            ))['total'];

            $messages = (int) mysqli_fetch_assoc(mysqli_query(
                $conn,
                'SELECT COUNT(*) AS total FROM messages WHERE id_destinataire = ' . $id_user
            ))['total'];

            $vuesRow = mysqli_fetch_assoc(mysqli_query(
                $conn,
                'SELECT SUM(vues) AS total FROM annonces WHERE id_user = ' . $id_user
            ));
            $vues = (int) ($vuesRow['total'] ?? 0);
        }
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/site.css?v=<?= filemtime(__DIR__ . '/assets/css/site.css') ?>">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="site-main">
    <h1>Mon compte</h1>

    <?php if ($erreur !== null): ?>
        <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php else: ?>

        <div class="compte-profil">
            <p class="compte-profil__nom">
                <?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?>
            </p>
            <p class="compte-profil__email"><?= htmlspecialchars($user['email']) ?></p>

            <?php if (!empty($user['date_naissance']) || !empty($user['ville'])): ?>
                <dl class="compte-profil__details">
                    <?php if (!empty($user['date_naissance'])): ?>
                        <dt>Date de naissance</dt>
                        <dd><?= htmlspecialchars($user['date_naissance']) ?></dd>
                    <?php endif; ?>
                    <?php if (!empty($user['ville'])): ?>
                        <dt>Ville</dt>
                        <dd><?= htmlspecialchars($user['ville']) ?></dd>
                    <?php endif; ?>
                </dl>
            <?php endif; ?>

            <div class="compte-profil__actions">
                <a href="deconnexion.php" class="btn-valider compte-profil__btn--secondaire">Déconnexion</a>
            </div>
        </div>

        <div class="grille compte-stats">
            <a href="mes_annonces.php" class="carte compte-stat">
                <h2>Annonces</h2>
                <p class="compte-stat__chiffre"><?= $annonces ?></p>
            </a>

            <a href="favoris.php" class="carte compte-stat">
                <h2>Favoris</h2>
                <p class="compte-stat__chiffre"><?= $favoris ?></p>
            </a>

            <a href="messages.php" class="carte compte-stat">
                <h2>Messages</h2>
                <p class="compte-stat__chiffre"><?= $messages ?></p>
            </a>

            <div class="carte compte-stat compte-stat--passif">
                <h2>Vues totales</h2>
                <p class="compte-stat__chiffre"><?= $vues ?></p>
            </div>
        </div>

    <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
