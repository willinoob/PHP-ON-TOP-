<?php
// On démarre avec une variable vide
$sql_injection = "";

// 1. Catégorie
if (isset($_GET['categorie']) && $_GET['categorie'] != "") {
    $sql_injection = $sql_injection . " AND a.id_cat = '" . $_GET['categorie'] . "'";
}

// 2. Marque
if (isset($_GET['marque']) && $_GET['marque'] != "") {
    $sql_injection = $sql_injection . " AND a.marque LIKE '%" . $_GET['marque'] . "%'";
}

// 3. Prix Maximum
if (isset($_GET['prix_max']) && $_GET['prix_max'] != "") {
    $sql_injection = $sql_injection . " AND a.prix <= '" . $_GET['prix_max'] . "'";
}

// 4. Kilométrage Maximum
if (isset($_GET['km_max']) && $_GET['km_max'] != "") {
    $sql_injection = $sql_injection . " AND a.kilometrage <= '" . $_GET['km_max'] . "'";
}

// 5. Carburant
if (isset($_GET['carburant']) && $_GET['carburant'] != "") {
    $sql_injection = $sql_injection . " AND a.carburant = '" . $_GET['carburant'] . "'";
}

// 6. Ville / Localisation
if (isset($_GET['ville']) && $_GET['ville'] != "") {
    $sql_injection = $sql_injection . " AND a.localisation LIKE '%" . $_GET['ville'] . "%'";
}
?>