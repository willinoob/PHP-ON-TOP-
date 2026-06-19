# Le Blog Moteur — Documentation du projet

**Type :** site d'annonces de véhicules d'occasion (PHP + MySQL, sans framework)  
**URL locale :** http://localhost/blogmoteur_annonces/page_annonces.php  
**Base de données :** `bd_blogmoteur`  
**Équipe :** Michel, Mehdi, William, Omar, Naomi

**Compte test :** `alice.test@blogmoteur.local` / `password`

---

## 1. But du site

c'est un site de revente et d'achat de voitures . les utilisaterus peuvent créer des comptes
et envoyer des messages concernant les annonces qui les intéresses .

| Profil | Actions principales |
|--------|---------------------|
| Visiteur | Liste, filtres, fiche détail |
| Connecté | + créer / modifier / supprimer ses annonces, favoris, messagerie, mon compte |
| Admin | + gestion utilisateurs et annonces |

---

## 2. Stratégie de codage

- **Un fichier `.php` = une page ou un traitement** (formulaire, redirection, action POST).
- **`includes/`** : morceaux communs (header, footer, favicon, gestion des photos).
- **`assets/css/`** : `base.css` (commun) + **un CSS par page** ; les petites pages réutilisent un CSS principal via `@import`.
- **MySQL** avec `mysqli_connect('localhost', 'root', '', 'bd_blogmoteur')` sur chaque page concernée.
- **Sessions PHP** (`$_SESSION['id_user']`, `$_SESSION['role_user']`) pour savoir qui est connecté.

### Sécurité (points clés)

- Requêtes **préparées** (`mysqli_prepare` + `bind_param`) sur connexion, inscription, messages, profil…
- Mots de passe **hashés** (`password_hash` / `password_verify`).
- Affichage sécurisé (`htmlspecialchars`).
- Vérification que l'utilisateur est **propriétaire** de l'annonce avant modification ou suppression.
- **Honeypot** anti-robot sur l'inscription (champ `url` invisible — Mehdi).

---

## 3. Répartition de l'équipe

| Membre | Périmètre |
|--------|-----------|
| **Michel** | Liste (F6), détail (F7), création annonce (F3), modification (F4), architecture CSS, mes annonces |
| **Omar** | Filtres (F8), favoris (F9) |
| **William** | Messagerie (`messages.php`, `envoyer.php`) , mon compte|
| **Mehdi** | Honeypot inscription, contributions admin |
| **Naomi** | — |

---

## 4. Fichiers `.php` — rôle de chacun

### Annonces et navigation

| Fichier | Rôle |
|---------|------|
| `page_annonces.php` | Page d'accueil : grille d'annonces + filtres |
| `detail_annonce.php` | Fiche détail : galerie, infos, favori, contact, compteur de vues |
| `form-filtres.php` | Formulaire HTML des filtres (inclus dans la liste) |
| `filtres-where.php` | Construit les `AND …` SQL à partir de l'URL (`?categorie=…`) |
| `creer_annonce.php` | Redirection vers `formulaire_creation_annonces.php` |

### Compte utilisateur

| Fichier | Rôle |
|---------|------|
| `connexion.php` | Connexion + création de session |
| `deconnexion.php` | Fin de session |
| `inscription_blogmoteur.php` | Inscription |
| `mon_compte.php` | Profil + statistiques (annonces, favoris, messages, vues) |
| `modifier_profil.php` | Modifier nom, prénom, email |
| `changer_mot_de_passe.php` | Changer le mot de passe |

### Gestion des annonces (connecté)

| Fichier | Rôle |
|---------|------|
| `formulaire_creation_annonces.php` | Créer une annonce + upload photo |
| `formulaire_modif_annonces.php` | Modifier une annonce (la sienne uniquement) |
| `mes_annonces.php` | Liste de ses annonces + boutons modifier / supprimer |
| `supprimer_annonce.php` | Traitement POST de suppression (annonce + fichiers photo) |

### Favoris

| Fichier | Rôle |
|---------|------|
| `favoris.php` | Page « Mes favoris » |
| `toggle-favori.php` | Ajoute ou retire un favori (POST), redirige vers le détail |

### Messagerie

| Fichier | Rôle |
|---------|------|
| `messages.php` | Interface type chat : liste des conversations + messages |
| `envoyer.php` | Enregistre un message (POST) et redirige |
| `fenetre_messages.php` | Contact vendeur **depuis une annonce** (formulaire + historique) |

> **Note :** `messages.php` et `fenetre_messages.php` sont deux entrées vers la même table `messages`. La première est la messagerie générale ; la seconde est le contact direct lié à une annonce précise.

### Administration

| Fichier | Rôle |
|---------|------|
| `pageadmin.php` | Liste users/annonces, bannir, supprimer, voir profil d'un utilisateur |

### Dossier `includes/`

| Fichier | Rôle |
|---------|------|
| `header.php` | Navigation (visiteur / connecté / admin) |
| `footer.php` | Pied de page |
| `favicon.php` | Icône du site |
| `photos.php` | Chemins photos pour l'affichage (espaces, accents, vignettes) |
| `traitement_image.php` | Upload, compression JPEG, création des vignettes `_thumb` |

---

## 5. Dossiers du projet

```
blogmoteur_annonces/
├── assets/
│   ├── css/                 → Feuilles de style (voir ci-dessous)
│   └── images/              → logo, favicon, image « pas de photo »
├── includes/                → PHP réutilisable
├── uploads/                 → Photos des annonces (+ vignettes _thumb)
├── sql/                     → Scripts d'installation MySQL
├── description du projet/   → README.md et ROADMAP.md
└── *.php                    → Pages et traitements à la racine
```

### `assets/css/` — organisation

Chaque page HTML charge **deux fichiers** :

```html
<link rel="stylesheet" href="assets/css/base.css?v=...">
<link rel="stylesheet" href="assets/css/nom_de_la_page.css?v=...">
```

Le `?v=` avec `filemtime()` évite que le navigateur garde un ancien CSS en cache.

| Fichier CSS | Type | Contenu |
|-------------|------|---------|
| `base.css` | Commun | Variables, header, footer, boutons |
| `page_annonces.css` | Principal | Grille, cartes, filtres |
| `detail_annonce.css` | Principal | Fiche détail, galerie |
| `connexion.css` | Principal | Pages centrées (boîte blanche) |
| `mon_compte.css` | Principal | Profil + stats (`@import` page_annonces) |
| `favoris.css` | Principal | Tableau favoris |
| `messages.css` | Principal | Interface messagerie |
| `fenetre_messages.css` | Principal | Contact vendeur |
| `formulaire_creation_annonces.css` | Principal | Formulaire création |
| `formulaire_modif_annonces.css` | Principal | Formulaire modification |
| `pageadmin.css` | Principal | Espace admin |
| `mes_annonces.css` | Secondaire | `@import` → `page_annonces.css` + boutons modifier/supprimer |
| `inscription_blogmoteur.css` | Secondaire | `@import` → `connexion.css` |
| `modifier_profil.css` | Secondaire | `@import` → `connexion.css` |
| `changer_mot_de_passe.css` | Secondaire | `@import` → `connexion.css` |

> Le fichier `site.css` à la racine de `assets/css/` est une **ancienne archive** : il n'est plus chargé par aucune page. Ne pas l'utiliser.

### `assets/images/`

| Fichier | Rôle |
|---------|------|
| `logo_blogmoteur.png` | Logo dans le header et pages connexion |
| `favicon.png` | Icône de l'onglet navigateur |
| `no picture.png` | Placeholder si une annonce n'a pas de photo |

### `uploads/`

Photos uploadées par les utilisateurs. Chaque photo génère aussi une vignette `nom_thumb.jpg` pour alléger la liste d'annonces.

---

## 6. Fichiers `.sql` (dossier `sql/`)

À exécuter dans **phpMyAdmin** (onglet SQL).

| Fichier | Utilité |
|---------|---------|
| `bd_blogmoteur_originale.sql` | **Installation principale** : crée la base, les tables et les données de départ. À importer en premier. |
| `seed-15-annonces.sql` | **Optionnel** : ajoute des annonces de démo pour tester filtres et liste. Les images doivent être dans `uploads/`. |
| `maj-chemins-photos.sql` | **Optionnel, dépannage** : corrige les chemins en base si les photos ne s'affichent plus. À adapter selon le nom du dossier local avant exécution. |

### Tables de la base

| Table | Rôle |
|-------|------|
| `users` | Comptes (email, mot de passe hashé, `role_user` pour admin) |
| `categories` | Voiture, moto, utilitaire… |
| `annonces` | Fiches véhicules |
| `photos` | Chemins des images (`ordre = 1` = photo principale) |
| `favoris` | Annonces sauvegardées par un utilisateur |
| `messages` | Échanges entre acheteur et vendeur |

**Ordre d'installation :** `bd_blogmoteur_originale.sql` → puis `seed-15-annonces.sql` si besoin.

---

## 7. Fonctions PHP importantes

### `includes/photos.php`

| Fonction | Rôle |
|----------|------|
| `chemin_photo_affichage()` | URL correcte pour les noms de fichiers avec espaces ou accents |
| `chemin_photo_vignette()` | Utilise la vignette `_thumb` si elle existe, sinon la photo normale |

### `includes/traitement_image.php`

| Fonction | Rôle |
|----------|------|
| `enregistrer_photo_upload()` | Upload, redimensionnement (max 1600×1200), compression JPEG, vignette |
| `redimensionner_image()` | Réduit l'image avec la bibliothèque GD |
| `supprimer_photos_annonce()` | Supprime photo + vignette du disque |

### Mécanismes utilisés dans les pages

| Mécanisme | Où | Pourquoi |
|-----------|-----|----------|
| `mysqli_prepare` / `bind_param` | connexion, inscription, envoyer… | Évite l'injection SQL |
| `password_hash` / `password_verify` | inscription, connexion, mdp | Mots de passe sécurisés |
| `$_SESSION['annonces_vues']` | detail_annonce.php | +1 vue par annonce et par session |
| `mysqli_insert_id` | création annonce | Récupère l'id pour lier la photo |
| `LEFT JOIN photos … ordre = 1` | liste, mes annonces | Une seule photo par carte |

---

## 8. Installation (XAMPP)

1. Copier le projet dans `C:\xampp\htdocs\blogmoteur_annonces`
2. Démarrer Apache et MySQL
3. phpMyAdmin → importer `sql/bd_blogmoteur_originale.sql`
4. (Optionnel) Exécuter `sql/seed-15-annonces.sql`
5. Ouvrir http://localhost/blogmoteur_annonces/page_annonces.php

Connexion MySQL : `localhost`, user `root`, mot de passe **vide**.

---

## 9. Non implémenté

- **Recherche AJAX** (bonus) : la recherche passe par les filtres GET et le rechargement de page.

---

*Le Blog Moteur — Documentation équipe, 2026.*
