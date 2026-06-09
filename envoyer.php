<?php
session_start();

$id = mysqli_connect("localhost", "william", "zigotodualbert1", "bd_blogmoteur");

$id_user = $_SESSION['id_user'];

$requete = "
SELECT DISTINCT
u.id_user,
u.nom,
u.prenom
FROM messages m, users u
WHERE
(
m.id_expediteur = '$id_user'
OR
m.id_destinataire = '$id_user'
)
AND
(
u.id_user = m.id_expediteur
OR
u.id_user = m.id_destinataire
)
";

$resultat = mysqli_query($id,$requete);

while($conv = mysqli_fetch_assoc($resultat))
{

    if($conv['id_user'] != $id_user)
    {
?>

<div class="conversation-item">

    <div class="conversation-name">
        <?php echo $conv['prenom']; ?>
        <?php echo $conv['nom']; ?>
    </div>

</div>

<?php
    }
}
?>