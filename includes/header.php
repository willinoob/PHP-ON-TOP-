<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$utilisateur_connecte = isset($_SESSION['id_user']);
?>
<header class="site-header<?= $utilisateur_connecte ? ' site-header--connecte' : '' ?>">
    <div class="site-header__inner">
        <a href="page_annonces.php" class="site-logo">
            <img src="assets/images/logo_blogmoteur.png" alt="Le Blog Moteur">
        </a>
        <nav class="site-nav" aria-label="Navigation principale">
            <a href="page_annonces.php" class="site-nav__link">Annonces</a>
            <?php if ($utilisateur_connecte): ?>
                <a href="mes_annonces.php" class="site-nav__link">Mes annonces</a>
                <a href="messages.php" class="site-nav__link">Messages</a>
                <a href="favoris.php" class="site-nav__link">Favoris</a>
                <a href="formulaire_creation_annonces.php" class="site-nav__link site-nav__link--cta">Déposer une annonce</a>
                <span class="site-nav__sep" aria-hidden="true"></span>
              <a href="mon_compte.php?id=<?= $_SESSION['user_id'] ?>" class="site-nav__link">
    Mon compte
</a>
                <a href="deconnexion.php" class="site-nav__link site-nav__link--secondaire">Déconnexion</a>
            <?php else: ?>
                <a href="connexion.php" class="site-nav__link site-nav__link--cta">Connexion</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
