<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$id_anno = isset($_GET['id_anno']) ? (int) $_GET['id_anno'] : 0;
$id_destinataire = isset($_GET['id_destinataire']) ? (int) $_GET['id_destinataire'] : 0;

$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');
$erreur = null;
$interlocuteur = null;
$titre_annonce = null;

if (!$conn) {
    $erreur = 'Impossible de se connecter à la base de données.';
} else {
    if ($id_anno > 0 && $id_destinataire <= 0) {
        $stmt = mysqli_prepare($conn, 'SELECT id_user, titre FROM annonces WHERE id_anno = ? LIMIT 1');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $id_anno);
            mysqli_stmt_execute($stmt);
            $annonce = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if ($annonce) {
                $titre_annonce = $annonce['titre'];
                $id_destinataire = (int) $annonce['id_user'];
            }
        }
    }

    if ($id_destinataire > 0) {
        if ($id_destinataire === $id_user) {
            $erreur = 'Vous ne pouvez pas vous envoyer un message à vous-même.';
            $id_destinataire = 0;
            $id_anno = 0;
        } else {
            $stmt = mysqli_prepare($conn, 'SELECT prenom, nom FROM users WHERE id_user = ? LIMIT 1');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id_destinataire);
                mysqli_stmt_execute($stmt);
                $interlocuteur = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);
            }
        }
    }

    if ($id_anno > 0 && $titre_annonce === null) {
        $stmt = mysqli_prepare($conn, 'SELECT titre FROM annonces WHERE id_anno = ? LIMIT 1');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $id_anno);
            mysqli_stmt_execute($stmt);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            if ($row) {
                $titre_annonce = $row['titre'];
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages — Le Blog Moteur</title>
    <link rel="stylesheet" href="assets/css/site.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="site-main site-main--messagerie">
    <h1>Mes messages</h1>

    <?php if ($erreur !== null): ?>
        <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <?php if ($conn): ?>
    <div class="messagerie">
        <aside class="messagerie__sidebar">
            <div class="messagerie__sidebar-header">
                <h2>Conversations</h2>
            </div>

            <div class="messagerie__liste">
                <?php
                $sql_conv = "SELECT DISTINCT u.id_user, u.prenom, u.nom,
                                    (SELECT m2.id_anno FROM messages m2
                                     WHERE (m2.id_expediteur = $id_user AND m2.id_destinataire = u.id_user)
                                        OR (m2.id_expediteur = u.id_user AND m2.id_destinataire = $id_user)
                                     ORDER BY m2.created_at DESC LIMIT 1) AS dernier_id_anno
                             FROM users u
                             INNER JOIN messages m ON (u.id_user = m.id_expediteur OR u.id_user = m.id_destinataire)
                             WHERE (m.id_expediteur = $id_user OR m.id_destinataire = $id_user)
                               AND u.id_user != $id_user
                             ORDER BY u.prenom ASC, u.nom ASC";

                $res_conv = mysqli_query($conn, $sql_conv);
                $a_conversations = false;

                if ($res_conv) {
                    while ($conv = mysqli_fetch_assoc($res_conv)) {
                        $a_conversations = true;
                        $conv_id = (int) $conv['id_user'];
                        $conv_anno = (int) ($conv['dernier_id_anno'] ?? 0);
                        $active = $conv_id === $id_destinataire ? ' messagerie__item--active' : '';
                        $href = 'messages.php?id_destinataire=' . $conv_id;
                        if ($conv_anno > 0) {
                            $href .= '&id_anno=' . $conv_anno;
                        }
                        ?>
                        <a href="<?= $href ?>" class="messagerie__item<?= $active ?>">
                            <span class="messagerie__item-nom">
                                <?= htmlspecialchars($conv['prenom'] . ' ' . $conv['nom']) ?>
                            </span>
                        </a>
                        <?php
                    }
                }

                if (!$a_conversations): ?>
                    <p class="messagerie__vide">Aucune conversation pour le moment.</p>
                <?php endif; ?>
            </div>
        </aside>

        <section class="messagerie__chat">
            <?php if ($id_destinataire > 0 && $interlocuteur): ?>
                <div class="messagerie__chat-header">
                    <h3><?= htmlspecialchars($interlocuteur['prenom'] . ' ' . $interlocuteur['nom']) ?></h3>
                    <?php if ($titre_annonce !== null): ?>
                        <p class="messagerie__annonce">Annonce : <?= htmlspecialchars($titre_annonce) ?></p>
                    <?php endif; ?>
                </div>

                <div class="messagerie__messages">
                    <?php
                    if ($id_anno > 0) {
                        $id_dest_sql = $id_destinataire;
                        $sql_msg = "SELECT id_expediteur, contenu, created_at
                                    FROM messages
                                    WHERE id_anno = $id_anno
                                      AND (
                                          (id_expediteur = $id_user AND id_destinataire = $id_dest_sql)
                                          OR (id_expediteur = $id_dest_sql AND id_destinataire = $id_user)
                                      )
                                    ORDER BY created_at ASC";
                        $res_msg = mysqli_query($conn, $sql_msg);

                        if ($res_msg && mysqli_num_rows($res_msg) > 0) {
                            while ($msg = mysqli_fetch_assoc($res_msg)) {
                                $moi = (int) $msg['id_expediteur'] === $id_user;
                                $classe = $moi ? 'messagerie__bulle--moi' : 'messagerie__bulle--autre';
                                ?>
                                <div class="messagerie__bulle <?= $classe ?>">
                                    <p class="messagerie__texte"><?= nl2br(htmlspecialchars($msg['contenu'])) ?></p>
                                    <time class="messagerie__heure"><?= htmlspecialchars($msg['created_at']) ?></time>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<p class="messagerie__vide">Aucun message. Envoyez le premier ci-dessous.</p>';
                        }
                    } else {
                        echo '<p class="messagerie__vide">Sélectionnez une conversation ou contactez un vendeur depuis une annonce.</p>';
                    }
                    ?>
                </div>

                <?php if ($id_anno > 0): ?>
                <form class="messagerie__form" method="post" action="envoyer.php">
                    <input type="hidden" name="id_anno" value="<?= $id_anno ?>">
                    <input type="hidden" name="id_destinataire" value="<?= $id_destinataire ?>">
                    <input type="text" name="contenu" class="messagerie__input" placeholder="Votre message…" required>
                    <button type="submit" class="messagerie__envoyer">Envoyer</button>
                </form>
                <?php endif; ?>

            <?php else: ?>
                <div class="messagerie__chat-vide">
                    <p>Sélectionnez une conversation à gauche, ou cliquez sur <strong>Contactez-nous</strong> depuis une annonce.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <?php mysqli_close($conn); ?>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
