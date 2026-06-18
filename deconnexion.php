<?php
// Déconnexion : vide la session puis retour à la liste
session_start();
session_unset();
session_destroy();
header('Location: page_annonces.php');
exit;
