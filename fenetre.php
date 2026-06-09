<?php
$conn = mysqli_connect("localhost", "william", "zigotodualbert1", "bd_blogmoteur");

if (!$conn) {
    die("Erreur de connexion à la base de données.");
}

$id = (int) $_GET['id'];

// Requête SQL (mysqli simple)
$sql = "
    SELECT a.id_anno, a.titre, a.id_user, u.nom AS nom_user
    FROM annonces a
    JOIN users u ON a.id_user = u.id_user
    WHERE a.id_anno = $id
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Erreur SQL : " . mysqli_error($conn));
}

$annonce = mysqli_fetch_assoc($result);

// Vérifier que l'annonce existe
if (!$annonce) {
    die("Annonce introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message</title>
    <link rel="stylesheet" href="sisi.css">
</head>
<body>

<form action="envoyer.php" method="POST">

    <input type="hidden" name="id_anno" value="<?php echo $annonce['id_anno']; ?>">
    <input type="hidden" name="id_destinataire" value="<?php echo $annonce['id_user']; ?>">

    <div class="message-container">

        <div class="message-header">
            <p><?php echo $annonce['nom_anno']; ?></p>
            <p><?php echo $annonce['nom_user']; ?></p>
        </div>

        <div class="message-content">

            <label for="message">Votre message</label>

            <textarea
                name="message"
                id="message"
                placeholder="Bonjour, votre annonce m'intéresse..."
                cols="50"
                rows="5"
                required
            ></textarea>

            <div class="actions">
                <button type="button" class="btn-annuler">Annuler</button>
                <button type="submit" class="btn-envoyer">Envoyer</button>
            </div>

        </div>

    </div>

</form>

</body>
</html>