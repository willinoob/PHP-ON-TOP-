<?php
/**
 * Messagerie (William) — liste des conversations + fil de messages par annonce.
 */
session_start();

$id = mysqli_connect("localhost", "root", "", "bd_blogmoteur");

if (!$id) {
    die("Erreur connexion");
}

if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Conversation choisie dans l'URL (?id_anno=…&id_destinataire=…)
$id_anno = (int)($_GET['id_anno'] ?? 0);
$id_destinataire = (int)($_GET['id_destinataire'] ?? 0);


// ======================
// RÉCUPÉRATION UTILISATEUR (DESTINATAIRE)
// ======================
$user = null;

if ($id_destinataire != 0) {

    $reqUser = "
    SELECT *
    FROM users
    WHERE id_user='$id_destinataire'
    ";

    $resUser = mysqli_query($id, $reqUser);

    $user = mysqli_fetch_assoc($resUser);
}


// ======================
// TITRE DE L'ANNONCE
// ======================
$titre_annonce = "";

if ($id_anno != 0) {

    $reqAnno = "
    SELECT titre
    FROM annonces
    WHERE id_anno='$id_anno'
    ";

    $resAnno = mysqli_query($id, $reqAnno);

    if ($row = mysqli_fetch_assoc($resAnno)) {
        $titre_annonce = $row['titre'];
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include __DIR__ . '/includes/favicon.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>

    <link rel="stylesheet" href="assets/css/base.css?v=<?= filemtime(__DIR__ . '/assets/css/base.css') ?>">
    <link rel="stylesheet" href="assets/css/messages.css?v=<?= filemtime(__DIR__ . '/assets/css/messages.css') ?>">
</head>

<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="site-main site-main--messagerie">

<div class="messagerie">

    <!-- ======================
         SIDEBAR
         ====================== -->
    <aside class="messagerie__sidebar">

        <div class="messagerie__sidebar-header">
            <h2>Conversations</h2>
        </div>

        <div class="messagerie__liste">

            <?php

            // Liste des personnes avec qui on a échangé (expéditeur OU destinataire)
            $requete = "
            SELECT DISTINCT
                u.id_user,
                u.prenom,
                u.nom,
                m.id_anno
            FROM users u, messages m
            WHERE
            (
                m.id_expediteur='$id_user'
                OR m.id_destinataire='$id_user'
            )
            AND
            (
                u.id_user=m.id_expediteur
                OR u.id_user=m.id_destinataire
            )
            AND u.id_user<>'$id_user'
            ";

            $resultat = mysqli_query($id, $requete);

            while ($conv = mysqli_fetch_assoc($resultat)) {
            ?>

                <a
                    href="messages.php?id_destinataire=<?php echo $conv['id_user']; ?>&id_anno=<?php echo $conv['id_anno']; ?>"
                    class="messagerie__item"
                >

                    <span class="messagerie__item-nom">
                        <?php echo $conv['prenom'] . " " . $conv['nom']; ?>
                    </span>

                </a>

            <?php
            }
            ?>

        </div>

    </aside>


    <!-- ======================
         CHAT
         ====================== -->
    <section class="messagerie__chat">

        <div class="messagerie__chat-header">

            <h3>
                <?php
                if ($user) {
                    echo $user['prenom'] . " " . $user['nom'];
                } else {
                    echo "Conversation";
                }
                ?>
            </h3>

            <p class="messagerie__annonce">
                <?php echo htmlspecialchars($titre_annonce); ?>
            </p>

        </div>


        <!-- ======================
             MESSAGES
             ====================== -->
        <div class="messagerie__messages">

            <?php

            if ($id_anno != 0 && $id_destinataire != 0) {

                // Messages entre moi et l'autre personne, pour cette annonce précise
                $requete = "                SELECT *
                FROM messages
                WHERE id_anno='$id_anno'
                AND
                (
                    (
                        id_expediteur='$id_user'
                        AND id_destinataire='$id_destinataire'
                    )
                    OR
                    (
                        id_expediteur='$id_destinataire'
                        AND id_destinataire='$id_user'
                    )
                )
                ORDER BY created_at ASC
                ";

                $resultat = mysqli_query($id, $requete);

                if (mysqli_num_rows($resultat) > 0) {

                    while ($msg = mysqli_fetch_assoc($resultat)) {

                        // Bulle à droite si c'est moi, à gauche si c'est l'autre
                        $moi = ($msg['id_expediteur'] == $id_user);
                        $classe = $moi ? "messagerie__bulle--moi" : "messagerie__bulle--autre";
            ?>

                        <div class="messagerie__bulle <?php echo $classe; ?>">

                            <p class="messagerie__texte">
                                <?php echo htmlspecialchars($msg['contenu']); ?>
                            </p>

                            <time class="messagerie__heure">
                                <?php echo $msg['created_at']; ?>
                            </time>

                        </div>

            <?php
                    }

                } else {
                    echo "<p class='messagerie__vide'>Aucun message.</p>";
                }

            } else {
                echo "<p class='messagerie__vide'>Sélectionne une conversation.</p>";
            }

            ?>

        </div>


        <!-- ======================
             ENVOI MESSAGE
             ====================== -->
        <form class="messagerie__form" method="POST" action="envoyer.php">

            <input
                type="text"
                name="contenu"
                class="messagerie__input"
                placeholder="Votre message..."
                required
            >

            <input type="hidden" name="id_anno" value="<?php echo $id_anno; ?>">
            <input type="hidden" name="id_destinataire" value="<?php echo $id_destinataire; ?>">

            <button type="submit" class="messagerie__envoyer">
                Envoyer
            </button>

        </form>

    </section>

</div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>

