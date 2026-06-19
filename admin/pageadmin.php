<?php
/**
 * Espace admin — réservé aux comptes avec role_user = 1 en base.
 */
session_start();

// Pas connecté → page connexion
if (!isset($_SESSION['id_user'])) {
    header('Location: ../mon_compte/connexion.php');
    exit;
}

if (($_SESSION['role_user'] ?? 0) !== 1) {
    // Connecté mais pas admin → retour liste publique
    header('Location: ../annonces/page_annonces.php');
    exit;
}

$conn = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');
$erreur = null;
$message_succes = null;

if (!$conn) {
    $erreur = 'Erreur de connexion : ' . mysqli_connect_error();
} else {
    
    // 2. TRAITEMENT DE LA SUPPRESSION D'UNE ANNONCE (Code de ta camarade)
    if (isset($_POST['supprimer_annonce'], $_POST['id_anno'])) {
        $idAnno = (int) $_POST['id_anno'];
        
        if (mysqli_query($conn, "DELETE FROM annonces WHERE id_anno = $idAnno")) {
            $message_succes = "L'annonce a été supprimée avec succès.";
        } else {
            $erreur = "Erreur lors de la suppression de l'annonce.";
        }
    }

    // 3. TRAITEMENT DU BANNISSEMENT DIRECT (Après confirmation par l'alerte de la page)
    if (isset($_GET['action']) && $_GET['action'] === 'ban_user' && isset($_GET['id_user'])) {
        $id_user_a_bannir = (int) $_GET['id_user'];
        
        if ($id_user_a_bannir === (int) $_SESSION['id_user']) {
            $erreur = "Vous ne pouvez pas vous bannir vous-même !";
        } else {
            $stmtBan = mysqli_prepare($conn, 'DELETE FROM users WHERE id_user = ?');
            if ($stmtBan) {
                mysqli_stmt_bind_param($stmtBan, 'i', $id_user_a_bannir);
                if (mysqli_stmt_execute($stmtBan)) {
                    $message_succes = "L'utilisateur a été banni avec succès et toutes ses données ont été supprimées.";
                } else {
                    $erreur = "Erreur lors du bannissement de l'utilisateur.";
                }
                mysqli_stmt_close($stmtBan);
            }
        }
    }

    // 4. RÉCUPÉRATION DES UTILISATEURS POUR LA LISTE
    $resUsers = mysqli_query($conn, 'SELECT id_user, prenom, nom, email, role_user FROM users ORDER BY nom, prenom');
    $utilisateurs = [];
    if (!$resUsers) {
        $erreur = 'Erreur SQL : ' . mysqli_error($conn);
    } else {
        while ($ligne = mysqli_fetch_assoc($resUsers)) {
            $utilisateurs[] = $ligne;
        }
    }

    // 5. RÉCUPÉRATION DES ANNONCES POUR LA LISTE
    $resAnnonces = mysqli_query(
        $conn,
        'SELECT a.id_anno, a.titre, a.prix, u.prenom, u.nom
         FROM annonces a
         INNER JOIN users u ON u.id_user = a.id_user
         ORDER BY a.created_at DESC'
    );
    $annonces = [];
    if ($resAnnonces) {
        while ($ligne = mysqli_fetch_assoc($resAnnonces)) {
            $annonces[] = $ligne;
        }
    }

    // 6. LOGIQUE D'AFFICHAGE DU PROFIL ET DES CONVERSATIONS
    $user_details = null;
    $nb_annonces_user = 0;
    $favoris_user = [];
    $messages_user = [];

    if (isset($_GET['voir_profil']) && $_GET['voir_profil'] != "") {
        // Panneau détail : stats + favoris + messages d'un utilisateur cliqué dans le tableau admin
        $id_user_selectionne = (int) $_GET['voir_profil'];

        $resDetail = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id_user_selectionne' LIMIT 1");
        if ($resDetail) {
            $user_details = mysqli_fetch_assoc($resDetail);
        }

        if ($user_details) {
            $resCount = mysqli_query($conn, "SELECT COUNT(*) AS total FROM annonces WHERE id_user = '$id_user_selectionne'");
            $dataCount = mysqli_fetch_assoc($resCount);
            $nb_annonces_user = $dataCount['total'];

            $resFav = mysqli_query($conn, "SELECT a.titre, a.prix FROM favoris f JOIN annonces a ON f.id_anno = a.id_anno WHERE f.id_user = '$id_user_selectionne'");
            while ($f = mysqli_fetch_assoc($resFav)) {
                $favoris_user[] = $f;
            }

            $resMsg = mysqli_query($conn, "SELECT m.*, u_exp.prenom AS exp_prenom, u_dest.prenom AS dest_prenom FROM messages m 
                JOIN users u_exp ON m.id_expediteur = u_exp.id_user 
                JOIN users u_dest ON m.id_destinataire = u_dest.id_user 
                WHERE m.id_expediteur = '$id_user_selectionne' OR m.id_destinataire = '$id_user_selectionne' 
                ORDER BY m.created_at DESC");
            while ($m = mysqli_fetch_assoc($resMsg)) {
                $messages_user[] = $m;
            }
        }
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include __DIR__ . '/../includes/favicon.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Admins — Le Blog Moteur</title>
    <link rel="stylesheet" href="../assets/css/base.css?v=<?= filemtime(__DIR__ . '/../assets/css/base.css') ?>">
    <link rel="stylesheet" href="../assets/css/pageadmin.css?v=<?= filemtime(__DIR__ . '/../assets/css/pageadmin.css') ?>">
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="site-main">
    <h1>Espace Admins</h1>

    <?php if ($erreur !== null): ?>
        <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <?php if ($message_succes !== null): ?>
        <div class="success-msg"><?= htmlspecialchars($message_succes) ?></div>
    <?php endif; ?>

    <section class="admin-section">
        <h2>Utilisateurs</h2>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['prenom']) ?></td>
                        <td><?= htmlspecialchars($u['nom']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= (int) $u['role_user'] === 1 ? 'Admin' : 'Client' ?></td>
                        <td>
                            <a href="pageadmin.php?voir_profil=<?= $u['id_user'] ?>" class="btn-action-view">Informations</a>

                            <?php if ((int)$u['id_user'] !== (int)$_SESSION['id_user']): ?>
                                <a href="pageadmin.php?id_user=<?= $u['id_user'] ?>&action=ban_user" class="btn-action-ban" onclick="return confirm('Voulez-vous vraiment supprimer le compte de l\'utilisateur <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?> ? Cela effacera toutes ses données.');"> Supprimer le compte</a>
                            <?php else: ?>
                                <span style="color: #888; font-style: italic;">Votre compte</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php if ($user_details): ?>
        <div class="user-profile-panel">
            <div class="panel-header">
                <h2>Détails et Conversations </h2>
            </div>

            <div class="grid-profile-infos">
                <div class="info-card">
                    <h3> Informations de compte</h3>
                    <p><strong>Nom :</strong> <?= htmlspecialchars($user_details['nom']) ?></p>
                    <p><strong>Prénom :</strong> <?= htmlspecialchars($user_details['prenom']) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($user_details['email']) ?></p>
                </div>
                <div class="info-card">
                    <h3> Activités </h3>
                    <p><strong>Nombre d'annonces :</strong> <span class="txt-highlight"><?= $nb_annonces_user ?></span></p>
                    <p><strong>Vues totales :</strong> <span class="txt-highlight"><?= $user_details['vues_total'] ?? '0' ?></span> vues</p>
                </div>
            </div>

            <div class="info-card-full">
                <h3>Annonces en favoris</h3>
                <?php if (count($favoris_user) > 0): ?>
                    <ul>
                        <?php foreach ($favoris_user as $f): ?>
                            <li><?= htmlspecialchars($f['titre']) ?> — <span class="txt-highlight"><?= number_format((float)$f['prix'], 2, ',', ' ') ?> €</span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p style="color: #aaa; font-style: italic;">Aucune annonce en favoris.</p>
                <?php endif; ?>
            </div>

            <div class="info-card-full">
                <h3>  Historique des conversations</h3>
                <?php if (count($messages_user) > 0): ?>
                    <div class="chat-history-box">
                        <?php foreach ($messages_user as $m): ?>
                            <?php 
                                $est_expediteur = ((int)$m['id_expediteur'] === (int)$id_user_selectionne);
                                $classe_msg = $est_expediteur ? 'msg-sent' : 'msg-received';
                                $nom_affiche = $est_expediteur ? 'Lui' : htmlspecialchars($m['exp_prenom']);
                                $nom_dest = $est_expediteur ? htmlspecialchars($m['dest_prenom']) : 'Lui';
                            ?>
                            <div class="msg-line <?= $classe_msg ?>">
                                <span class="msg-date">[<?= $m['created_at'] ?>]</span>
                                <strong><?= $nom_affiche ?> ➔ <?= $nom_dest ?> :</strong>
                                <span><?= htmlspecialchars($m['contenu']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #aaa; font-style: italic;">Aucun message ou échange trouvé pour cet utilisateur.</p>
                <?php endif; ?>
            </div>
            
            <?php if ((int)$user_details['id_user'] !== (int)$_SESSION['id_user']): ?>
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="pageadmin.php?id_user=<?= $user_details['id_user'] ?>&action=ban_user" class="btn-confirm-del" onclick="return confirm('Voulez-vous vraiment bannir définitivement cet utilisateur ? Cela effacera toutes ses données.');">
                            Supprimer le compte de cet utilisateur
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <section class="admin-section">
        <h2>Annonces</h2>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Prix</th>
                        <th>Vendeur</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($annonces as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['titre']) ?></td>
                        <td><?= number_format((float) $a['prix'], 2, ',', ' ') ?> €</td>
                        <td><?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?></td>
                        <td>
                            <a href="../annonces/detail_annonce.php?id_anno=<?= (int) $a['id_anno'] ?>">Voir</a>

                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_anno" value="<?= (int) $a['id_anno'] ?>">
                                <button type="submit" name="supprimer_annonce" class="btn-supprimer-annonce" onclick="return confirm('Voulez-vous vraiment supprimer cette annonce ?');">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>