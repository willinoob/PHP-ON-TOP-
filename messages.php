<?php
session_start();

$id = mysqli_connect("localhost", "william", "zigotodualbert1", "bd_blogmoteur");

if (!$id) {
    die("Erreur connexion");
}

$id_user = $_SESSION['id_user'];

$id_anno = (int) ($_GET['id_anno'] ?? 0);
$id_destinataire = (int) ($_GET['id_destinataire'] ?? 0);

/* ===================== AJOUT IMPORTANT ===================== */
// récupération user du chat
$user = null;

if ($id_destinataire != 0) {
    $reqUser = "SELECT * FROM users WHERE id_user='$id_destinataire'";
    $resUser = mysqli_query($id, $reqUser);
    $user = mysqli_fetch_assoc($resUser);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <!-- ===================== SIDEBAR ===================== -->
    <div class="sidebar">

        <div class="sidebar-header">
            <h2>Mes messages</h2>
            <input type="text" placeholder="Rechercher..." class="search-input">
        </div>

        <div class="conversation-list">

            <?php

            $requete = "
            SELECT DISTINCT u.*
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
            ";

            $resultat = mysqli_query($id, $requete);

            while ($conv = mysqli_fetch_assoc($resultat)) {
                if ($conv['id_user'] != $id_user) {
            ?>

            <div class="conversation-item">

                <div class="conversation-name">
                    <?php echo $conv['prenom']." ".$conv['nom']; ?>
                </div>

                <div class="conversation-preview">
                    Conversation
                </div>

                <div class="conversation-time">
                    ...
                </div>

            </div>

            <?php
                }
            }
            ?>

        </div>

    </div>

    <!-- ===================== CHAT ===================== -->
    <div class="chat">

        <div class="chat-header">

            <h3>
                <?php echo isset($user) ? $user['prenom']." ".$user['nom'] : "Conversation"; ?>
            </h3>

        </div>

        <!-- ===================== MESSAGES ===================== -->
        <div class="messages">

            <?php

            $requete = "
            SELECT *
            FROM messages
            WHERE id_anno='$id_anno'
            ORDER BY created_at ASC
            ";

            $resultat = mysqli_query($id, $requete);

            while ($msg = mysqli_fetch_assoc($resultat)) {
            ?>

            <div class="message">

                <div class="bubble">

                    <div class="message-text">
                        <?php echo $msg['contenu']; ?>
                    </div>

                    <div class="message-time">
                        <?php echo $msg['created_at']; ?>
                    </div>

                </div>

            </div>

            <?php
            }
            ?>

        </div>

        <!-- ===================== INPUT ===================== -->
        <div class="input-area">

            <form class="input-group" method="POST" action="envoyer.php">

                <input
                    type="text"
                    name="contenu"
                    placeholder="Votre message..."
                    class="message-input"
                    required
                >

                <input type="hidden" name="id_anno" value="<?php echo $id_anno; ?>">
                <input type="hidden" name="id_expediteur" value="<?php echo $id_user; ?>">
                <input type="hidden" name="id_destinataire" value="<?php echo $id_destinataire; ?>">

                <button class="send-button" type="submit">
                    Envoyer
                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>