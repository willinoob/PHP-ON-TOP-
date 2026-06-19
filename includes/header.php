<?php
/**
 * Header commun — navigation dynamique selon que l'utilisateur est connecté ou non.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$utilisateur_connecte = isset($_SESSION['id_user']);

// Page courante (ex : "page_annonces.php") pour surligner le lien de nav actif
$page_courante = basename($_SERVER['PHP_SELF']);

/**
 * Renvoie les classes du lien de nav, en ajoutant "is-active" si on est sur la page.
 * @param string|array $cibles fichier(s) qui doivent activer ce lien
 */
function nav_classe($cibles, $page_courante, $extra = '') {
    $cibles = (array) $cibles;
    $classes = 'site-nav__link';
    if ($extra !== '') {
        $classes .= ' ' . $extra;
    }
    if (in_array($page_courante, $cibles, true)) {
        $classes .= ' is-active';
    }
    return $classes;
}
?>
<header class="site-header<?= $utilisateur_connecte ? ' site-header--connecte' : '' ?>">
    <div class="site-header__inner">
        <a href="page_annonces.php" class="site-logo">
            <img src="assets/images/logo_blogmoteur.png" alt="Le Blog Moteur">
        </a>
        <nav class="site-nav" aria-label="Navigation principale">
            <a href="page_annonces.php" class="<?= nav_classe(['page_annonces.php', 'detail_annonce.php'], $page_courante) ?>">Annonces</a>
            <?php if ($utilisateur_connecte): ?>
                <a href="mes_annonces.php" class="<?= nav_classe(['mes_annonces.php', 'formulaire_modif_annonces.php'], $page_courante) ?>">Mes annonces</a>
                <a href="messages.php" class="<?= nav_classe(['messages.php', 'fenetre_messages.php'], $page_courante) ?>">Messages</a>
                <a href="favoris.php" class="<?= nav_classe('favoris.php', $page_courante) ?>">Favoris</a>
                <a href="formulaire_creation_annonces.php" class="<?= nav_classe(['formulaire_creation_annonces.php', 'creer_annonce.php'], $page_courante, 'site-nav__link--cta') ?>">Déposer une annonce</a>
                <span class="site-nav__sep" aria-hidden="true"></span>
                <a href="mon_compte.php" class="<?= nav_classe(['mon_compte.php', 'modifier_profil.php', 'changer_mot_de_passe.php'], $page_courante) ?>">Mon compte</a>
                <?php if (isset($_SESSION['role_user']) && (int) $_SESSION['role_user'] === 1): ?>
                    <!-- role_user = 1 en base → compte administrateur -->
                    <a href="pageadmin.php" class="<?= nav_classe('pageadmin.php', $page_courante) ?>">Admin</a>
                <?php endif; ?>
                <a href="deconnexion.php" class="site-nav__link site-nav__link--secondaire">Déconnexion</a>
            <?php else: ?>
                <a href="connexion.php" class="<?= nav_classe(['connexion.php', 'inscription_blogmoteur.php'], $page_courante, 'site-nav__link--cta') ?>">Connexion</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
