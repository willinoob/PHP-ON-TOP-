<?php
/*
  Explication du fichier fenetre_messages.php

  1) session_start()
      Démarre la session PHP pour accéder aux informations de l'utilisateur connecté (ex: $_SESSION['id_user']).

  2) Connexion à la base de données
      mysqli_connect("localhost", "root", "", "bd_blogmoteur") : création d'une connexion MySQL.

  3) Récupération de l'annonce
      - id_anno = $_GET['id_anno'] : identifiant de l'annonce passé en paramètre GET.
      - Requête préparée pour sélectionner l'annonce et les informations de l'utilisateur (INNER JOIN users).
      - Exécution et récupération avec mysqli_prepare / mysqli_stmt_bind_param / mysqli_stmt_execute / mysqli_stmt_get_result.
      - Si aucune annonce trouvée, on arrête le script (die).

  4) Envoi du message
      - Si le formulaire est soumis (isset($_POST['envoyer'])) : on récupère le contenu.
      - id_expediteur = $_SESSION['id_user'] (utilisateur connecté) ; id_destinataire = propriétaire de l'annonce.
      - Insertion d'un nouveau message dans la table messages via requête préparée (paramètres liés pour éviter les injections SQL).
      - Affichage d'un message de confirmation.

  5) Historique des messages
      - Sélection de tous les messages liés à l'annonce (WHERE id_anno = ?) triés par created_at ASC.
      - Requête préparée puis récupération du résultat pour affichage.

  6) Partie HTML
      - Affiche les informations de l'annonce (titre, prix, vendeur).
      - Formulaire POST avec textarea pour écrire un message et bouton d'envoi.
      - Boucle while pour afficher la conversation (contenu et created_at pour chaque message).
      - Lien pour revenir à la page des annonces.

  Remarques de sécurité et bonnes pratiques (non modifiées ici) :
      - Vérifier que l'utilisateur est connecté avant d'utiliser $_SESSION['id_user'].
      - Échapper les sorties HTML (htmlspecialchars) pour éviter les failles XSS lors de l'affichage des données (titre, contenu, noms, etc.).
      - Gérer les erreurs de connexion à la base et les retours d'erreur des requêtes.

*/
session_start();

$connexion = mysqli_connect("localhost", "root", "root", "bd_blogmoteur");

$id_anno = $_GET['id_anno'];

/* Récupération de l'annonce */

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
        "iiis",
        $id_anno,
        $id_expediteur,
        $id_destinataire,
        $contenu
    );

    mysqli_stmt_execute($stmt);

    echo "<p style='color:green;'>Message envoyé avec succès.</p>";
}

/* Historique des messages */

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
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<title>Messagerie</title>

<style>

body{
    font-family:Arial,sans-serif;
    background:#f4f4f4;
}

.fenetre{
    width:700px;
    margin:30px auto;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.2);
}

textarea{
    width:100%;
    height:120px;
    padding:10px;
    resize:none;
}

input[type="submit"]{
    margin-top:10px;
    padding:10px 20px;
    cursor:pointer;
}

.message{
    background:#eeeeee;
    padding:10px;
    margin-top:10px;
    border-radius:5px;
}

small{
    color:gray;
}

a{
    text-decoration:none;
}

</style>

</head>

<body>

<div class="fenetre">

    <h2>Contacter le vendeur</h2>

    <h3>
        <?php echo $annonce['titre']; ?>
    </h3>

    <p>
        Prix :
        <?php echo $annonce['prix']; ?> €
    </p>

    <p>
        Vendeur :
        <?php echo $annonce['prenom']; ?>
        <?php echo $annonce['nom']; ?>
    </p>

    <form method="POST">

        <textarea
            name="contenu"
            placeholder="Écrivez votre message..."
            required
        ></textarea>

        <br>

        <input
            type="submit"
            name="envoyer"
            value="Envoyer"
        >

    </form>

    <hr>

    <h3>Conversation</h3>

    <?php while($message = mysqli_fetch_assoc($resultat_messages)) { ?>

        <div class="message">

            <p>
                <?php echo $message['contenu']; ?>
            </p>

            <small>
                <?php echo $message['created_at']; ?>
            </small>

        </div>

    <?php } ?>

    <br>

    <a href="page_annonces.php">
        ← Retour aux annonces
    </a>

</div>

</body>
</html>