<?php
// F8 (Omar) — conditions SQL à partir des filtres GET
$sql_injection = '';

$filtre_cat = isset($_GET['categorie']) ? (int) $_GET['categorie'] : 0;
$filtre_prix_max = isset($_GET['prix_max']) && $_GET['prix_max'] !== ''
    ? (float) str_replace(',', '.', $_GET['prix_max'])
    : null;

if ($filtre_cat > 0) {
    $sql_injection .= ' AND a.id_cat = ' . $filtre_cat;
}

if ($filtre_prix_max !== null) {
    $sql_injection .= ' AND a.prix <= ' . $filtre_prix_max;
}
