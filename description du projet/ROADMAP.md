# Le Blog Moteur — Roadmap

**Objectif :** répartition des tâches pour **demain** — chaque membre est responsable de **toutes** les pages et fonctionnalités de son périmètre (PHP + CSS associé).

**Référence technique :** voir `README.md` dans ce dossier.

---

## Vue d'ensemble

| Membre | Périmètre |
|--------|-----------|
| **Michel** | Annonces (liste, détail, création, mes annonces, favoris, suppression) |
| **William** | Messagerie + Mon compte (profil, mot de passe) |
| **Omar** | Filtrage des annonces + Administration |
| **Mehdi** | Inscription + Administration |
| **Naomi** | Connexion + Modification d'annonce |

> **Admin (`pageadmin.php`)** : Omar et Mehdi travaillent ensemble sur la même page — se répartir les sections (utilisateurs / annonces) et se coordonner pour ne pas modifier en conflit.

---

## Michel — Annonces

Tout ce qui concerne les annonces, **avant ou après** la connexion d'un utilisateur.

### Fichiers PHP

| Fichier | Rôle |
|---------|------|
| `page_annonces.php` | Page d'accueil, grille d'annonces |
| `detail_annonce.php` | Fiche détail, galerie, compteur de vues, contact vendeur |
| `formulaire_creation_annonces.php` | Créer une annonce + upload photo |
| `creer_annonce.php` | Redirection vers le formulaire de création |
| `mes_annonces.php` | Liste des annonces de l'utilisateur connecté |
| `supprimer_annonce.php` | Traitement POST de suppression |
| `favoris.php` | Page « Mes favoris » |
| `toggle-favori.php` | Ajouter / retirer un favori (POST) |

### Includes liés

| Fichier | Rôle |
|---------|------|
| `includes/photos.php` | Chemins et vignettes pour l'affichage des photos |
| `includes/traitement_image.php` | Upload, compression JPEG, vignettes `_thumb` |

### Fichiers CSS

`page_annonces.css` · `detail_annonce.css` · `mes_annonces.css` · `favoris.css` · `formulaire_creation_annonces.css`

### À vérifier demain

- [ ] Visiteur : parcourir la liste et ouvrir une fiche détail
- [ ] Connecté : créer une annonce avec photo
- [ ] Connecté : Mes annonces → supprimer une annonce
- [ ] Connecté : ajouter / retirer un favori depuis le détail
- [ ] Propriétaire : sur **sa** annonce, pas de bouton contact ni favori pour soi-même
- [ ] Photos : affichage correct (espaces dans les noms, vignettes en liste)

---

## William — Messagerie & Mon compte

Tout ce qui concerne les messages et l'espace personnel de l'utilisateur connecté.

### Fichiers PHP

| Fichier | Rôle |
|---------|------|
| `messages.php` | Messagerie générale (liste des conversations) |
| `fenetre_messages.php` | Contact vendeur depuis une annonce |
| `envoyer.php` | Enregistrement d'un message (POST) |
| `mon_compte.php` | Tableau de bord (stats, liens) |
| `modifier_profil.php` | Modifier nom, prénom, email |
| `changer_mot_de_passe.php` | Changer le mot de passe |

### Fichiers CSS

`messages.css` · `fenetre_messages.css` · `mon_compte.css` · `modifier_profil.css` · `changer_mot_de_passe.css`

### À vérifier demain

- [ ] Connecté : envoyer un message depuis la messagerie générale
- [ ] Connecté : contacter un vendeur depuis une fiche annonce (`fenetre_messages.php`)
- [ ] Connecté : Mon compte affiche les bonnes statistiques
- [ ] Connecté : modifier le profil et le mot de passe
- [ ] Visiteur non connecté : redirection correcte si accès interdit

---

## Omar — Filtrage & Administration

Tout ce qui concerne les filtres de la liste d'annonces et l'espace admin (avec Mehdi).

### Fichiers PHP

| Fichier | Rôle |
|---------|------|
| `form-filtres.php` | Formulaire HTML des filtres (catégorie, prix, etc.) |
| `filtres-where.php` | Construction des conditions SQL à partir de l'URL |
| `pageadmin.php` | Espace administrateur (avec Mehdi) |

### Fichiers CSS

`pageadmin.css` · styles des filtres dans `page_annonces.css` (coordonner avec Michel si besoin)

### À vérifier demain

- [ ] Filtres : catégorie, prix min/max, combinaisons multiples
- [ ] Filtres : UX lisible et responsive sur mobile
- [ ] Admin : accès réservé (`role_user = 1`)
- [ ] Admin : actions sur les annonces (voir, supprimer…)
- [ ] Admin : se coordonner avec Mehdi sur `pageadmin.php`

### Bonus *(non obligatoire)*

| ID | Tâche | Fichier(s) |
|----|-------|------------|
| **B1** | Barre de recherche AJAX sans rechargement | `page_annonces.php`, `filtres-where.php` |

---

## Mehdi — Inscription & Administration

Tout ce qui concerne l'inscription des nouveaux utilisateurs et l'espace admin (avec Omar).

### Fichiers PHP

| Fichier | Rôle |
|---------|------|
| `inscription_blogmoteur.php` | Formulaire et traitement d'inscription |
| `pageadmin.php` | Espace administrateur (avec Omar) |

### Includes liés

| Fichier | Rôle |
|---------|------|
| Honeypot dans `inscription_blogmoteur.php` | Champ `url` invisible anti-robot |

### Fichiers CSS

`inscription_blogmoteur.css` · `pageadmin.css`

### À vérifier demain

- [ ] Inscription : création de compte, validation des champs
- [ ] Inscription : honeypot bloque les robots
- [ ] Inscription : mot de passe hashé, email unique
- [ ] Admin : actions sur les utilisateurs (voir profil, bannir…)
- [ ] Admin : se coordonner avec Omar sur `pageadmin.php`

---

## Naomi — Connexion & Modification d'annonce

Tout ce qui concerne la connexion / déconnexion et l'édition d'une annonce existante.

### Fichiers PHP

| Fichier | Rôle |
|---------|------|
| `connexion.php` | Connexion + création de session |
| `deconnexion.php` | Fin de session |
| `formulaire_modif_annonces.php` | Modifier une annonce (propriétaire uniquement) |

### Fichiers CSS

`connexion.css` · `formulaire_modif_annonces.css`

### À vérifier demain

- [ ] Connexion : email + mot de passe, redirection après login
- [ ] Connexion : message d'erreur si identifiants incorrects
- [ ] Déconnexion : session détruite, retour page d'accueil
- [ ] Modification : seul le propriétaire peut éditer son annonce
- [ ] Modification : champs pré-remplis, photo remplaçable si besoin
- [ ] Visiteur : redirection vers connexion si non connecté

---

## Fichiers communs (toute l'équipe)

Ces fichiers sont partagés — chacun met à jour **uniquement** les liens et éléments liés à son périmètre.

| Fichier | Rôle |
|---------|------|
| `includes/header.php` | Navigation (visiteur / connecté / admin) |
| `includes/footer.php` | Pied de page |
| `includes/favicon.php` | Icône du site |
| `assets/css/base.css` | Styles communs (header, footer, boutons) |
| `assets/images/` | Logo, favicon, image « pas de photo » |

**Règle :** avant de modifier `header.php` ou `base.css`, prévenir le groupe sur le chat.

---

## Parcours complet à tester (toute l'équipe)

- [ ] **Visiteur** : liste → filtres → détail → contact (connexion si besoin)
- [ ] **Nouveau** : inscription → connexion
- [ ] **Connecté** : déposer une annonce → Mes annonces → modifier → supprimer
- [ ] **Connecté** : favori + messagerie sur l'annonce d'un autre
- [ ] **Connecté** : Mon compte → modifier profil → changer mot de passe
- [ ] **Admin** : accès back-office, gestion users et annonces

---

## Statut global

| Symbole | Signification |
|---------|---------------|
| ⬜ | À faire / à vérifier demain |
| 🔄 | En cours |
| ✅ | Terminé et testé |

Chaque membre coche ses cases dans la section **À vérifier demain** au fur et à mesure.

---

*Le Blog Moteur — Roadmap équipe, 2026.*
