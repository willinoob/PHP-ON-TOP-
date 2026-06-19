<?php
/**
 * Contact vendeur — envoyer un message lié à une annonce + voir l'historique.
 */
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: ../mon_compte/connexion.php');
    exit;
}

$connexion = mysqli_connect("localhost", "root", "root", "bd_blogmoteur");

$id_anno = (int) ($_GET['id_anno'] ?? 0);

/* Récupération de l'annonce + infos du vendeur (requête préparée avec ?) */

$sql = "
SELECT annonces.*, users.nom, users.prenom
FROM annonces
INNER JOIN users
ON annonces.id_user = users.id_user
WHERE id_anno = ?
";

$stmt = mysqli_prepare($connexion, $sql);

mysqli_stmt_bind_param($stmt, "i", $id_anno);

mysqli_stmt_execute($stmt);

$resultat = mysqli_stmt_get_result($stmt);

$annonce = mysqli_fetch_assoc($resultat);

if (!$annonce)
{
    die("Annonce introuvable");
}

/* Envoi du message */

$succes_envoi = false;

if (isset($_POST['envoyer']))
{
    $contenu = $_POST['contenu'];

    $id_expediteur = $_SESSION['id_user'];
    $id_destinataire = $annonce['id_user'];

    $sql = "
    INSERT INTO messages
    (
        id_anno,
        id_expediteur,
        id_destinataire,
        contenu
    )
    VALUES (?, ?, ?, ?)
    ";

    $stmt = mysqli_prepare($connexion, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "iiis", // 3 entiers (annonce, expéditeur, destinataire) + 1 texte (contenu)
        $id_anno,
        $id_expediteur,
        $id_destinataire,
        $contenu
    );

    mysqli_stmt_execute($stmt);

    $succes_envoi = true;

    $stmt = mysqli_prepare($connexion, "
    SELECT *
    FROM messages
    WHERE id_anno = ?
    ORDER BY created_at ASC
    ");
    mysqli_stmt_bind_param($stmt, "i", $id_anno);
    mysqli_stmt_execute($stmt);
    $resultat_messages = mysqli_stmt_get_result($stmt);
}

/* Si on n'a pas encore chargé les messages (pas d'envoi POST), on les lit une fois */
if (!isset($resultat_messages)) {
    $sql = "
    SELECT *
    FROM messages
    WHERE id_anno = ?
    ORDER BY created_at ASC
    ";

    $stmt = mysqli_prepare($connexion, $sql);

    mysqli_stmt_bind_param($stmt, "i", $id_anno);

    mysqli_stmt_execute($stmt);

    $resultat_messages = mysqli_stmt_get_result($stmt);
}

mysqli_close($connexion);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include __DIR__ . '/../includes/favicon.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacter le vendeur — Le Blog Moteur</title>
    <link rel="stylesheet" href="../assets/css/base.css?v=<?= filemtime(__DIR__ . '/../assets/css/base.css') ?>">
    <link rel="stylesheet" href="../assets/css/fenetre_messages.css?v=<?= filemtime(__DIR__ . '/../assets/css/fenetre_messages.css') ?>">
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="site-main contact-fenetre">
    <a href="../annonces/detail_annonce.php?id_anno=<?= (int) $id_anno ?>" class="contact-fenetre__retour">&larr; Retour à l'annonce</a>

    <h1>Contacter le vendeur</h1>

    <div class="contact-fenetre__annonce">
        <h2><?= htmlspecialchars($annonce['titre']) ?></h2>
        <p class="contact-fenetre__prix"><?= number_format((float) $annonce['prix'], 2, ',', ' ') ?> €</p>
        <p class="contact-fenetre__vendeur">
            Vendeur : <strong><?= htmlspecialchars($annonce['prenom'] . ' ' . $annonce['nom']) ?></strong>
        </p>
    </div>

    <?php if ($succes_envoi): ?>
        <p class="succes contact-fenetre__succes">Message envoyé avec succès.</p>
    <?php endif; ?>

    <form method="post" class="contact-fenetre__form">
        <label for="contenu" class="contact-fenetre__label">Votre message</label>
        <textarea
            id="contenu"
            name="contenu"
            class="contact-fenetre__textarea"
            placeholder="Écrivez votre message au vendeur…"
            required
        ></textarea>
        <button type="submit" name="envoyer" class="btn-valider contact-fenetre__envoyer">Envoyer</button>
    </form>

    <section class="contact-fenetre__historique">
        <h2>Conversation</h2>

        <?php if (mysqli_num_rows($resultat_messages) === 0): ?>
            <p class="vide contact-fenetre__vide">Aucun message pour le moment.</p>
        <?php else: ?>
            <?php while ($message = mysqli_fetch_assoc($resultat_messages)): ?>
                <article class="contact-fenetre__message">
                    <p class="contact-fenetre__message-texte"><?= htmlspecialchars($message['contenu']) ?></p>
                    <time class="contact-fenetre__message-date"><?= htmlspecialchars($message['created_at']) ?></time>
                </article>
            <?php endwhile; ?>
        <?php endif; ?>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>