# Le Blog Moteur — Roadmap

**Objectif :** terminer le site avant **mardi**

Chaque membre choisit une tâche, note son nom dans **Assigné à**, prévient le groupe, puis code.

---

## Déjà fait

| Fonctionnalité | Fichier(s) |
|----------------|------------|
| Liste annonces (F6) | `page_annonces.php` |
| Détail annonce (F7) + compteur de vues | `detail_annonce.php` |
| Filtres catégorie / prix (F8) | `form-filtres.php`, `filtres-where.php` |
| Amélioration formulaire de filtrage (F2) | `form-filtres.php`, `filtres-where.php`, `assets/css/site.css` |
| Favoris (F9) | `favoris.php`, `toggle-favori.php` |
| Connexion / déconnexion (F2) | `connexion.php`, `deconnexion.php` |
| Inscription | `inscription_blogmoteur.php` |
| Mon compte + profil | `mon_compte.php`, `modifier_profil.php`, `changer_mot_de_passe.php` |
| Page admin (début) | `pageadmin.php` |
| Création annonce + upload photo (F3) | `formulaire_creation_annonces.php` |
| Messagerie | `messages.php`, `envoyer.php` |
| Contact vendeur (visiteur → connexion → messages) | `detail_annonce.php`, `connexion.php`, `messages.php` |
| Nav dynamique (connecté / visiteur) | `includes/header.php` |
| Header + footer communs | `includes/header.php`, `includes/footer.php` |
| Charte graphique bleu-nuit & cuivre | `assets/css/site.css` |

---

## À faire

| ID | Tâche | Description | Fichier(s) | Assigné à | Statut |
|----|-------|-------------|------------|-----------|--------|
| **D1** | Agrandir l’image sur la fiche détail | Photos plus grandes et lisibles sur la page détail | `detail_annonce.php` | | ✅ |
| **D2** | Règle propriétaire sur la fiche détail | Sur **sa propre annonce**, masquer les actions acheteur (contact et favori) | `detail_annonce.php` | | ✅ |
| **D3** | Compresser les images à l’upload | Réduire le poids des photos uploadées pour ne pas ralentir le site | `formulaire_creation_annonces.php` | | ✅ |
| **N1** | « Mes annonces » dans la nav | Lien visible si connecté → liste des annonces postées par l’utilisateur | `includes/header.php`, `mes_annonces.php` (à créer) | | ✅ |
| **F2** | Amélioration du formulaire de filtrage | Améliorer UX/UI des filtres (champs, alignement, responsive, lisibilité) | `form-filtres.php`, `assets/css/site.css` | Omar | ✅ |
| **C1** | Mon compte — modifier le profil | Modifier nom, prénom, email, téléphone (champ pas présent à l’inscription) | `mon_compte.php`, `modifier_profil.php`, `changer_mot_de_passe.php` | William | ✅ |
| **C2** | Supprimer une annonce | Supprimer **ses** annonces depuis Mon compte ou Mes annonces | `mon_compte.php` ou `mes_annonces.php` | | ⬜ |
| **C3** | Modifier une annonce (F4) | Formulaire pour éditer une annonce existante | `formulaire_modification_annonces.php` | | ✅ |
| **F1** | Champ photo stylé | Zone d’upload plus moderne sur le formulaire de création (aperçu, design) | `formulaire_creation_annonces.php`, `assets/css/site.css` | | ✅ |
| **L1** | Changer le logo | Remplacer le logo du site | `assets/images/`, `includes/header.php` | | ✅ |
| **A1** | Page d’administration | Espace admin : gérer utilisateurs et annonces | `pageadmin.php` | William | 🔄 |
| **B1** | Barre de recherche AJAX *(bonus)* | Recherche sans recharger la page — **non obligatoire** | `page_annonces.php` | | ⬜ |

**Statut :** ⬜ À faire · 🔄 En cours · ✅ Terminé

---

## Parcours à tester avant mardi

- [ ] Visiteur : liste → détail → contact (connexion si besoin)
- [ ] Connecté : déposer une annonce
- [ ] Connecté : Mes annonces → modifier → supprimer
- [ ] Connecté : favori + messagerie sur annonce d’un autre
- [ ] Connecté : Mon compte → modifier profil
- [ ] Admin : accès et actions back-office
