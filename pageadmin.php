<?php
session_start();
 $id = mysqli_connect("localhost", "root", "root", "bd_blogmoteur");
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: page_annonces.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pageadmin</title>
</head>
<header>Administration</header>
<body>
    <div class="deroulement">
        <section id="user">
        <h1>liste des utilisateurs</h1>
    $req = "SELECT prenom, nom FROM users";
    $res = mysqli_query($id, $req);

    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Info</th>
            <th>Action</th>
          </tr>";
              while ($ligne = mysqli_fetch_assoc($res)) {
        echo "<tr>";
        echo "<td>" . $ligne['prenom'] . "</td>";
        echo "<td>" . $ligne['nom'] . "</td>";
        echo "<td></td>";      // colonne vide (info)
        echo "<td></td>";      // colonne vide (action)
        echo "</tr>";
    }

    echo "</table>";
    ?>

        </section>
        <section id="annonces">

        </section>



    </div>
</body>
</html>