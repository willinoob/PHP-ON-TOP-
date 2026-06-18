<?php
/**
 * Mon compte — profil + statistiques (annonces, favoris, messages, vues).
 */
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
            // Petites requêtes COUNT / SUM pour les chiffres des cartes en bas
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
            // ?? 0 : si l'utilisateur n'a pas d'annonce, SUM renvoie NULL
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
    <?php include __DIR__ . '/includes/favicon.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/base.css?v=<?= filemtime(__DIR__ . '/assets/css/base.css') ?>">
    <link rel="stylesheet" href="assets/css/mon_compte.css?v=<?= filemtime(__DIR__ . '/assets/css/mon_compte.css') ?>">
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

            <dl class="compte-profil__details">
                <dt>Nom</dt>
                <dd><?= htmlspecialchars($user['nom']) ?></dd>
                <dt>Prénom</dt>
                <dd><?= htmlspecialchars($user['prenom']) ?></dd>
                <dt>Email</dt>
                <dd><?= htmlspecialchars($user['email']) ?></dd>
            </dl>

            <div class="compte-profil__actions">
                <a href="modifier_profil.php" class="btn-valider">Modifier le profil</a>
                <a href="changer_mot_de_passe.php" class="btn-valider compte-profil__btn--outline">Changer le mot de passe</a>
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
