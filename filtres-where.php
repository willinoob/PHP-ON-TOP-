<?php
// Omar — F8 : conditions SQL des filtres

$filtre_cat = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
$filtre_prix_max = isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? (float)$_GET['prix_max'] : null;
$filtre_km_max = isset($_GET['km_max']) && $_GET['km_max'] !== '' ? (int)$_GET['km_max'] : null;
$filtre_carburant = isset($_GET['carburant']) ? $_GET['carburant'] : '';

$filtres_sql = "";

if ($filtre_cat > 0) {
    $filtres_sql .= " AND a.id_cat = " . $filtre_cat;
}

if ($filtre_prix_max !== null) {
    $filtres_sql .= " AND a.prix <= " . $filtre_prix_max;
}

if ($filtre_km_max !== null) {
    $filtres_sql .= " AND a.kilometrage <= " . $filtre_km_max;
}

if (!empty($filtre_carburant)) {
    $carburant_safe = mysqli_real_escape_string($conn, $filtre_carburant);
    $filtres_sql .= " AND a.carburant = '" . $carburant_safe . "'";
}

// On passe les filtres à la variable attendue par la requête principale
$sql_injection = $filtres_sql;