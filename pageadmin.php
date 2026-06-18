<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

if (($_SESSION['role_user'] ?? 0) !== 1) {
    header('Location: page_annonces.php');
    exit;
}

$conn = mysqli_connect('localhost', 'root', 'root', 'bd_blogmoteur');
$erreur = null;
$utilisateurs = [];
$annonces = [];

if (!$conn) {
    $erreur = 'Erreur de connexion : ' . mysqli_connect_error();
} else {
    $resUsers = mysqli_query($conn, 'SELECT id_user, prenom, nom, email, role_user FROM users ORDER BY nom, prenom');
    if (!$resUsers) {
        $erreur = 'Erreur SQL : ' . mysqli_error($conn);
    } else {
        while ($ligne = mysqli_fetch_assoc($resUsers)) {
            $utilisateurs[] = $ligne;
        }
    }

    $resAnnonces = mysqli_query(
        $conn,
        'SELECT a.id_anno, a.titre, a.prix, u.prenom, u.nom
         FROM annonces a
         INNER JOIN users u ON u.id_user = a.id_user
         ORDER BY a.created_at DESC'
    );
    if ($resAnnonces) {
        while ($ligne = mysqli_fetch_assoc($resAnnonces)) {
            $annonces[] = $ligne;
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
    <title>Administration — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/site.css?v=<?= filemtime(__DIR__ . '/assets/css/site.css') ?>">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="site-main">
    <h1>Administration</h1>

    <?php if ($erreur !== null): ?>
        <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php else: ?>

        <section class="admin-section">
            <h2>Utilisateurs</h2>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['prenom']) ?></td>
                            <td><?= htmlspecialchars($u['nom']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= (int) $u['role_user'] === 1 ? 'Admin' : 'Membre' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-section">
            <h2>Annonces</h2>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Prix</th>
                            <th>Vendeur</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($annonces as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['titre']) ?></td>
                            <td><?= number_format((float) $a['prix'], 2, ',', ' ') ?> €</td>
                            <td><?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?></td>
                            <td>
                                <a href="detail_annonce.php?id_anno=<?= (int) $a['id_anno'] ?>">Voir</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

    <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
