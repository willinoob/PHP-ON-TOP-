# Le Blog Moteur — Guide du code (Michè + Omar + William)

**URL liste :** http://localhost/blogmoteur_annonces/page_annonces.php  
**URL détail :** http://localhost/blogmoteur_annonces/detail_annonce.php?id_anno=1  
**URL favoris :** http://localhost/blogmoteur_annonces/favoris.php  
**URL connexion :** http://localhost/blogmoteur_annonces/connexion.php  
**URL création annonce :** http://localhost/blogmoteur_annonces/formulaire_creation_annonces.php  
**URL mon compte :** http://localhost/blogmoteur_annonces/mon_compte.php  
**URL messages :** http://localhost/blogmoteur_annonces/messages.php  
**URL envoi message :** `envoyer.php` (traitement POST, redirection automatique)  
**Base de données :** `bd_blogmoteur` (tables `annonces`, `categories`, `photos`, `favoris`, `users`, `messages`)

Ce document explique le code du projet : **Michè** (F6 liste, F7 détail, bonus vues, F3 création), **Omar** (F8 filtres, F9 favoris), **William** (messagerie), **compte** (F2 connexion, inscription, nav dynamique), et **comment tout est branché**.

**Compte test :** `alice.test@blogmoteur.local` / `password`

---

## Installation (XAMPP)

1. Copier le dossier dans `C:\xampp\htdocs\blogmoteur_annonces`
2. Démarrer **Apache** et **MySQL** dans le panneau XAMPP
3. Importer la base via phpMyAdmin :
   - fichier principal : `bd_blogmoteur.sql`
   - données de démo (optionnel) : `sql/seed-15-annonces.sql`
   - correction chemins photos (si besoin) : `sql/maj-chemins-photos.sql`
4. Ouvrir http://localhost/blogmoteur_annonces/page_annonces.php

Connexion MySQL utilisée partout : `localhost`, user `root`, mot de passe vide, base `bd_blogmoteur`.

---

## Arborescence du projet

```
blogmoteur_annonces/
├── page_annonces.php              # F6 — liste + filtres
├── detail_annonce.php             # F7 — fiche détail + vues + favori
├── formulaire_creation_annonces.php  # F3 — créer une annonce
├── creer_annonce.php              # redirection vers le formulaire F3
├── connexion.php / deconnexion.php   # F2
├── form-filtres.php / filtres-where.php  # F8 (Omar)
├── favoris.php / toggle-favori.php       # F9 (Omar)
├── messages.php / envoyer.php            # Messagerie (William)
├── inscription_blogmoteur.php              # Inscription
├── mon_compte.php                          # espace compte (placeholder)
├── includes/header.php / footer.php
├── assets/css/site.css
├── assets/images/logo-blogmoteur.svg
├── uploads/                       # photos uploadées
├── bd_blogmoteur.sql
└── sql/                           # scripts complémentaires
```

---

## Statut des fonctionnalités

| Fonctionnalité | Statut |
|----------------|--------|
| F6 — Liste annonces | ✅ |
| F7 — Détail annonce + compteur vues | ✅ |
| F8 — Filtres (catégorie, prix max) | ✅ |
| F9 — Favoris (toggle + page) | ✅ |
| F2 — Connexion sécurisée | ✅ |
| F3 — Création annonce + upload photo | ✅ |
| Nav dynamique (header) | ✅ |
| F4 — Modification annonce |  |
| Inscription (`inscription_blogmoteur.php`) | ✅ |
| Messagerie (`messages.php` + `envoyer.php`) | ✅ |
| Contact vendeur (détail → connexion → messages) | ✅ |
| Mon compte (`mon_compte.php`) |  |
| Barre de recherche AJAX |  |

**Note technique :** `detail_annonce.php` simule encore `id_user = 1` (Alice) si l’utilisateur n’est pas connecté, pour tester le bouton favori. Les pages `favoris.php`, `formulaire_creation_annonces.php`, `messages.php` et `mon_compte.php` exigent une vraie session.

---

## Équipe

| Membre | Périmètre |
|--------|-----------|
| **Michè** | F6 liste, F7 détail, bonus vues, F3 création, layout |
| **Omar** | F8 filtres, F9 favoris |
| **William** | Messagerie (`messages.php`, `envoyer.php`) |

---

## Vue d’ensemble

| Fichier | Auteur | Rôle |
|---------|--------|------|
| `page_annonces.php` | Michè (+ Omar intégré) | Liste en cartes + formulaire de filtres |
| `detail_annonce.php` | Michè (+ Omar intégré) | Fiche détail + compteur vues + bouton favori |
| `form-filtres.php` | Omar | Formulaire HTML des filtres (inclus dans la liste) |
| `filtres-where.php` | Omar | Construit les conditions SQL `AND …` à partir de l’URL |
| `favoris.php` | Omar | Page « Mes favoris » (cartes des annonces sauvegardées) |
| `toggle-favori.php` | Omar | Traitement POST : ajoute ou retire un favori en base |
| `connexion.php` | Compte | Connexion sécurisée (F2) |
| `deconnexion.php` | Compte | Détruit la session et renvoie vers la liste |
| `includes/header.php` | Layout | Nav dynamique (visiteur vs connecté) |
| `includes/footer.php` | Layout | Pied de page commun |
| `messages.php` | William | Interface messagerie (sidebar + chat) |
| `envoyer.php` | William | INSERT message + redirection |
| `inscription_blogmoteur.php` | Compte | Inscription sécurisée |
| `formulaire_creation_annonces.php` | Michè | Créer une annonce — formulaire + traitement (F3) |
| `creer_annonce.php` | Compte | Redirige vers `formulaire_creation_annonces.php` |
| `mon_compte.php` | Compte | Placeholder espace compte |
| `assets/css/site.css` | Layout | Styles communs (nav, cartes, formulaires) |

### Flux utilisateur complet

```
page_annonces.php
  ├── includes/header.php   (nav dynamique selon session)
  ├── form-filtres.php      (filtres GET : catégorie, prix, km, carburant)
  ├── filtres-where.php     ($sql_injection dans la requête)
  ├── cartes → detail_annonce.php?id_anno=X
  └── includes/footer.php

connexion.php → page_annonces.php (même page, nav enrichie)

Visiteur (non connecté) — nav :
  Annonces | Connexion

Connecté — nav :
  Annonces | Messages | Favoris | Mon compte | Déposer une annonce | Déconnexion

Contact vendeur (visiteur) :
  detail_annonce.php → Contactez-nous → connexion.php?redirect=messages&id_anno=X
  → après login → messages.php?id_anno=X (vendeur détecté automatiquement)
  → envoyer.php (POST) → retour messages.php avec la conversation
```

### Intégration (comment Michè et Omar se connectent)

| Où | Ce qui a été ajouté |
|----|---------------------|
| `page_annonces.php` | `require filtres-where.php` après connexion ; `include form-filtres.php` avant la grille ; `WHERE 1=1` + `$sql_injection` dans le SELECT ; header/footer communs |
| `detail_annonce.php` | SELECT sur table `favoris` ; formulaire POST vers `toggle-favori.php` ; page autonome (CSS inline, pas de header.php) |
| `includes/header.php` | `session_start()` si besoin ; nav différente si `isset($_SESSION['id_user'])` |
| `favoris.php` | Header/footer communs ; redirection vers `connexion.php` si non connecté |
| `detail_annonce.php` | Bouton **Contactez-nous** (visiteur uniquement) → `connexion.php` puis `messages.php` |
| `messages.php` | Code William intégré : sidebar conversations + zone chat + formulaire d’envoi |

---

## Fonctions PHP utilisées (référence rapide)

### Fonction écrite par nous

| Fonction | Fichiers | Rôle |
|----------|----------|------|
| `chemin_photo_affichage($chemin)` | `page_annonces.php`, `detail_annonce.php`, `favoris.php` | Transforme un chemin fichier en URL sûre pour le navigateur. |
| `champ_val($valeurs, $nom)` | `formulaire_creation_annonces.php` | Réaffiche une valeur du formulaire échappée (`htmlspecialchars`) après erreur. |

### Fonctions PHP natives (mysqli)

| Fonction | Rôle |
|----------|------|
| `mysqli_connect(hôte, user, mdp, bdd)` | Ouvre une connexion MySQL. |
| `mysqli_connect_error()` | Message d’erreur si connexion échouée. |
| `mysqli_query($conn, $sql)` | Envoie une requête SQL. |
| `mysqli_fetch_assoc($result)` | Lit une ligne en tableau associatif. |
| `mysqli_num_rows($result)` | Compte les lignes du résultat. |
| `mysqli_error($conn)` | Message d’erreur SQL. |
| `mysqli_real_escape_string($conn, $texte)` | Échappe une chaîne pour l’injecter en SQL (carburant). |
| `mysqli_prepare($conn, $sql)` | Prépare une requête SQL avec `?` comme marqueurs (anti-injection). |
| `mysqli_stmt_bind_param($stmt, 's', $var)` | Lie une variable PHP à un `?` (`s` = string, `i` = int). |
| `mysqli_stmt_execute($stmt)` | Exécute la requête préparée. |
| `mysqli_stmt_get_result($stmt)` | Récupère le résultat (comme `mysqli_query`). |
| `mysqli_stmt_close($stmt)` | Ferme la requête préparée. |
| `mysqli_insert_id($conn)` | Retourne l’ID auto-généré après un `INSERT` (ex. nouvelle annonce). |
| `mysqli_close($conn)` | Ferme la connexion. |

### Autres fonctions PHP natives

| Fonction | Rôle |
|----------|------|
| `htmlspecialchars($texte)` | Échappe le HTML (protection XSS). |
| `rawurlencode($texte)` | Encode les caractères spéciaux dans une URL. |
| `number_format($n, 2, ',', ' ')` | Formate un prix : `9500` → `9 500,00`. |
| `strlen` / `substr` | Tronquer la description sur les cartes (100 caractères). |
| `empty` / `isset` | Tester si une variable existe ou est vide. |
| `(int)` / `(float)` | Convertir en nombre (sécurise les IDs et filtres). |
| `nl2br` | Retours à la ligne → `<br>` en HTML. |
| `session_start()` | Démarre la session PHP (vues + favoris). |
| `password_verify($saisi, $hash)` | Compare un mot de passe saisi avec le hash en base. |
| `header('Location: …')` | Redirection HTTP (après toggle favori). |
| `exit` | Arrête le script immédiatement. |
| `require` / `include` | Inclure un autre fichier PHP. |
| `$_GET[…]` | Paramètres dans l’URL (`?categorie=1&prix_max=15000`). |
| `$_POST[…]` | Données envoyées par formulaire POST. |
| `$_SESSION[…]` | Données persistantes pendant la visite (session). |
| `$_FILES[…]` | Fichier envoyé par formulaire (`enctype="multipart/form-data"`). |
| `move_uploaded_file($tmp, $dest)` | Déplace la photo uploadée vers `uploads/`. |

---

## Explication de `chemin_photo_affichage()` (commune à plusieurs fichiers)

Les photos en base ont un chemin du type `uploads/Peugeot 208.jpg`. Les espaces et accents doivent être encodés pour l’URL.

```php
function chemin_photo_affichage($chemin) {
    if (empty($chemin)) return '';
    $pos = strrpos($chemin, '/');
    if ($pos === false) return htmlspecialchars(rawurlencode($chemin));
    $dossier = substr($chemin, 0, $pos + 1);   // "uploads/"
    $fichier = substr($chemin, $pos + 1);        // "Peugeot 208.jpg"
    return htmlspecialchars($dossier . rawurlencode($fichier));
}
```

---

# Partie Michè — F6 : `page_annonces.php`

## Structure du fichier (après intégration Omar)

```
Lignes 1-23    → PHP : fonction photo, connexion, filtres-where.php
Lignes 25-33   → HTML + lien site.css
Lignes 35-40   → header.php, titre, include form-filtres.php
Lignes 42-90   → PHP : requête SQL filtrée, boucle cartes
Lignes 95-97   → footer.php + fermeture HTML
```

## Lignes 1 à 23 — PHP en tête de fichier

| Ligne(s) | Code | Explication |
|----------|------|-------------|
| 1-14 | `function chemin_photo_affichage` | Voir section commune. |
| 17 | `$conn = mysqli_connect(...)` | Connexion MySQL XAMPP. |
| 18 | `$sql_injection = ''` | Valeur par défaut si pas de filtre actif. |
| 20-22 | `require filtres-where.php` | Omar : lit les GET et remplit `$sql_injection`. **Nécessite `$conn` ouvert.** |

## Lignes 35 à 40 — Layout, titre, filtres

| Ligne | Explication |
|-------|-------------|
| 35 | `include header.php` → nav dynamique (visiteur / connecté). |
| 38 | Titre `<h1>Liste des annonces</h1>`. |
| 40 | `include form-filtres.php` si connexion OK → affiche le formulaire de filtres. |
| 95 | `include footer.php` → pied de page commun. |

## Lignes 47 à 54 — Requête SQL principale (avec filtres)

```sql
SELECT a.id_anno, a.titre, a.prix, a.description, a.localisation,
       c.nom AS categorie, p.chemin
FROM annonces a
INNER JOIN categories c ON c.id_cat = a.id_cat
LEFT JOIN photos p ON p.id_anno = a.id_anno AND p.ordre = 1
WHERE 1=1 AND a.prix <= 15000 AND a.carburant = 'essence'
ORDER BY a.created_at DESC
```

| Partie | Signification |
|--------|---------------|
| `INNER JOIN categories` | Chaque annonce a une catégorie (obligatoire). |
| `LEFT JOIN photos … ordre = 1` | Première photo seulement (optionnelle). |
| `WHERE 1=1` | Astuce : permet d’ajouter des `AND …` via `$sql_injection` sans gérer un WHERE vide. |
| `$sql_injection` | Chaîne construite par `filtres-where.php` (Omar). |
| `ORDER BY a.created_at DESC` | Plus récentes en premier. |

## Lignes 64 à 86 — Boucle cartes

Identique à la version d’origine : une ligne SQL = une carte `<article>`, lien vers `detail_annonce.php?id_anno=X`, description tronquée à 100 caractères.

---

# Partie Michè — F7 : `detail_annonce.php`

## Structure du fichier (après intégration Omar)

```
Ligne 1          → session_start()
Lignes 2-29      → HTML + CSS + navigation
Lignes 31-112    → PHP : id, connexion, SELECT, vues, favori, photos
Lignes 115-161   → Affichage HTML
Ligne 163        → mysqli_close
```

## Ligne 1 — Session

```php
<?php session_start(); ?>
```

Obligatoire **avant tout HTML**. Utilisée pour :
- **Bonus vues** : `$_SESSION['annonces_vues']`
- **Favoris Omar** : `$_SESSION['id_user']`

## Lignes 50 à 54 — Session utilisateur (F9 Omar)

```php
if (!isset($_SESSION['id_user'])) {
    $_SESSION['id_user'] = 1;  // fallback test si pas connecté
}
$id_user_connecte = (int) $_SESSION['id_user'];
```

Sur la fiche détail, un fallback simule Alice si l’utilisateur n’est pas connecté (pour tester les favoris). **En production**, on redirigerait vers `connexion.php`. Les pages `favoris.php` et `formulaire_creation_annonces.php` exigent une vraie connexion.

## Lignes 90 à 99 — Bonus compteur de vues (anti-F5)

```php
if (empty($_SESSION['annonces_vues'][$id_anno])) {
    mysqli_query($conn, 'UPDATE annonces SET vues = vues + 1 WHERE id_anno = ' . $id_anno);
    $annonce['vues'] = (int) $annonce['vues'] + 1;
    $_SESSION['annonces_vues'][$id_anno] = true;
}
```

| Comportement | Résultat |
|--------------|----------|
| 1ère visite de l’annonce dans la session | +1 en base |
| F5 / rechargement | Pas de +1 (déjà mémorisé en session) |
| Nouvelle session navigateur | +1 à nouveau |

**À dire au prof :** une vraie visite par annonce et par session, pas un compteur à chaque requête HTTP.

## Lignes 101 à 109 — Vérifier si favori (F9 Omar)

```sql
SELECT id_fav FROM favoris
WHERE id_user = $id_user_connecte AND id_anno = $id_anno
LIMIT 1
```

Si une ligne existe → `$est_favori = true` → le bouton affiche « Retirer des favoris ».

## Lignes 118 à 123 — Bouton favori (intégration Omar)

```html
<form method="POST" action="toggle-favori.php">
    <input type="hidden" name="id_anno" value="3">
    <button type="submit">☆ Ajouter aux favoris</button>
</form>
```

- **POST** (pas GET) : modifier la base ne se fait pas via un simple lien.
- `id_anno` caché : le script `toggle-favori.php` sait quelle annonce traiter.
- Libellé dynamique selon `$est_favori`.

---

# Partie Omar — F8 : Filtrage

## Fichier `filtres-where.php` (ligne par ligne)

| Ligne(s) | Code | Explication |
|----------|------|-------------|
| 4 | `$filtre_cat = … $_GET['categorie'] … (int)` | Lit la catégorie choisie (0 = toutes). |
| 5 | `$filtre_prix_max = … (float)` | Prix max ; `null` si champ vide. |
| 6 | `$filtre_km_max = … (int)` | Kilométrage max ; `null` si vide. |
| 7 | `$filtre_carburant = … $_GET['carburant']` | Chaîne : essence, diesel, electrique. |
| 9 | `$filtres_sql = ""` | Chaîne SQL à construire. |
| 11-13 | `if ($filtre_cat > 0)` | Ajoute ` AND a.id_cat = X`. |
| 15-17 | `if ($filtre_prix_max !== null)` | Ajoute ` AND a.prix <= X`. |
| 19-21 | `if ($filtre_km_max !== null)` | Ajoute ` AND a.kilometrage <= X`. |
| 23-26 | carburant | `mysqli_real_escape_string` puis ` AND a.carburant = '…'`. |
| 29 | `$sql_injection = $filtres_sql` | Variable lue par `page_annonces.php`. |

**Sécurité :** nombres castés en `(int)` / `(float)` ; texte carburant échappé avec `mysqli_real_escape_string`.

**Exemple d’URL filtrée :**

```
page_annonces.php?categorie=1&prix_max=15000&km_max=80000&carburant=essence
```

## Fichier `form-filtres.php` (ligne par ligne)

| Ligne(s) | Explication |
|----------|-------------|
| 5-8 | Relit les GET pour **pré-remplir** le formulaire après filtrage. |
| 11 | `SELECT * FROM categories` → alimente le `<select>` catégorie. **Nécessite `$conn`.** |
| 14-23 | CSS du bloc filtres (zone blanche, boutons bleu/gris). |
| 26 | `<form method="GET" action="page_annonces.php">` | Envoie les filtres dans l’URL (pas en POST). |
| 30-37 | Menu déroulant catégories ; `selected` si filtre actif. |
| 42-47 | Champs numériques prix max et km max. |
| 52-57 | Menu carburant (essence / diesel / électrique). |
| 60 | Bouton « Filtrer » → soumet le formulaire. |
| 62-64 | Lien « Effacer les filtres » → retour à `page_annonces.php` sans paramètres. |

---

# Partie Omar — F9 : Favoris

## Table `favoris` en base

```
favoris
────────
id_fav (PK)
id_user     → users.id_user
id_anno     → annonces.id_anno
created_at
```

Contrainte `UNIQUE (id_user, id_anno)` : un utilisateur ne peut pas mettre deux fois la même annonce en favori.

## Fichier `toggle-favori.php` (ligne par ligne)

| Ligne(s) | Explication |
|----------|-------------|
| 3 | `session_start()` pour lire `$_SESSION['id_user']`. |
| 5-8 | Si pas de session user ou pas de `id_anno` en POST → redirection liste. |
| 11-12 | IDs convertis en entiers. |
| 18 | `SELECT id_fav FROM favoris WHERE …` → le favori existe-t-il ? |
| 20-22 | Oui → `DELETE` (retirer des favoris). |
| 24-25 | Non → `INSERT INTO favoris (id_user, id_anno)`. |
| 32-33 | Redirection vers `detail_annonce.php?id_anno=X` (l’utilisateur revoit la fiche avec le bouton mis à jour). |

**Pourquoi POST + redirection ?** Pattern « traitement puis redirect » : évite de re-soumettre le formulaire au F5.

## Fichier `favoris.php` (ligne par ligne)

| Ligne(s) | Explication |
|----------|-------------|
| 3-7 | Si pas de session → redirection `connexion.php`. |
| 10 | `$id_user_connecte` = ID de l'utilisateur connecté. |
| 13-18 | Fonction `chemin_photo_affichage` (comme la liste). |
| 30-31 | `include header.php` + `footer.php`. |
| 40-47 | Requête SQL des favoris de l’utilisateur connecté. |

```sql
SELECT a.id_anno, a.titre, a.prix, …
FROM favoris f
INNER JOIN annonces a ON f.id_anno = a.id_anno
INNER JOIN categories c ON c.id_cat = a.id_cat
LEFT JOIN photos p ON p.id_anno = a.id_anno AND p.ordre = 1
WHERE f.id_user = $id_user_connecte
ORDER BY f.created_at DESC
```

| Partie | Signification |
|--------|---------------|
| `FROM favoris f` | Point de départ : les favoris de l’utilisateur. |
| `INNER JOIN annonces` | Récupère les infos de chaque annonce favorite. |
| `WHERE f.id_user = …` | Uniquement **mes** favoris. |
| `ORDER BY f.created_at DESC` | Dernier ajouté en premier. |

| Ligne(s) | Explication |
|----------|-------------|
| 50-51 | Aucun favori → message « Aucune annonce enregistrée… ». |
| 53-67 | Boucle identique à la liste Michè : cartes cliquables vers le détail. |

---

# Schéma des tables utilisées

```
users               categories          annonces              photos
─────               ──────────          ────────              ──────
id_user (PK)        id_cat (PK)    ←──  id_cat                id_photo (PK)
nom, email…         nom                 id_anno (PK)     ←──  id_anno
                                        id_user          ←──  users
                                        titre, prix…            chemin, ordre
                                        vues

                    favoris
                    ───────
                    id_fav (PK)
                    id_user  → users
                    id_anno  → annonces
                    created_at
```

---

# Connexion sécurisée — `connexion.php` (F2, niveau soutenance)

> **Ce qu’on montre au prof :** pas de SQL concaténé avec `$_POST`, requête **préparée**, mot de passe vérifié avec **`password_verify`**, erreurs affichées avec **`htmlspecialchars`**.

## Ancien code (à ne plus faire)

```php
// DANGEREUX — injection SQL possible
$requete = "SELECT * FROM users WHERE email = '$email'";
mysqli_query($conn, $requete);
```

L’email de l’utilisateur est collé directement dans la requête → un attaquant peut modifier la requête.

---

## Nouveau code — requête préparée (anti-injection SQL)

```php
$sql = 'SELECT id_user, nom, prenom, password FROM users WHERE email = ? LIMIT 1';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$resultat = mysqli_stmt_get_result($stmt);
```

| Fonction | Rôle |
|----------|------|
| `mysqli_prepare` | Prépare la requête ; le `?` sera remplacé par la variable liée, jamais concaténé dans la chaîne SQL. |
| `mysqli_stmt_bind_param($stmt, 's', $email)` | Lie `$email` au premier `?` (`s` = string). |
| `mysqli_stmt_execute` | Exécute la requête avec la valeur liée → **injection SQL impossible** sur l’email. |
| `mysqli_stmt_get_result` | Récupère les lignes comme avec `mysqli_query`. |
| `mysqli_stmt_close` | Libère la requête préparée. |

### Mot de passe hashé

```php
if ($user && password_verify($mdp, $user['password'])) {
    $_SESSION['id_user'] = (int) $user['id_user'];
    $_SESSION['prenom']  = $user['prenom'];
    $_SESSION['nom']     = $user['nom'];
    header('Location: page_annonces.php');
}
```

| Point | Explication |
|-------|-------------|
| En base | Le mot de passe est stocké **hashé** (`password_hash` à l’inscription). |
| À la connexion | `password_verify` compare la saisie au hash — jamais de mdp en clair en base. |
| Session | `id_user`, `prenom` et `nom` sont stockés pour la nav et `mon_compte.php`. |

### Affichage sécurisé

```php
<?= htmlspecialchars($erreur) ?>
```

Évite d’injecter du HTML/JavaScript via un message d’erreur (XSS).

### Résumé oral (20 secondes)

> « On n’écrit plus l’email dans la requête SQL. On utilise `mysqli_prepare` avec un `?`, et `bind_param` lie la variable. Pour le mot de passe, on utilise `password_verify` sur le hash en base. »

---

# Navigation dynamique — `includes/header.php`

Après connexion, l’utilisateur revient sur **`page_annonces.php`** : mêmes annonces, mêmes filtres, même footer. **Seule la nav change.**

## Code principal

```php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$utilisateur_connecte = isset($_SESSION['id_user']);
```

| Variable | Signification |
|----------|---------------|
| `session_status() === PHP_SESSION_NONE` | Aucune session active → on appelle `session_start()`. |
| `$utilisateur_connecte` | `true` si l’utilisateur s’est connecté (`$_SESSION['id_user']` existe). |

## Nav visiteur

```php
<a href="page_annonces.php">Annonces</a>
<a href="connexion.php">Connexion</a>
```

## Nav connecté

```php
<a href="page_annonces.php">Annonces</a>
<a href="messages.php">Messages</a>
<a href="favoris.php">Favoris</a>
<a href="mon_compte.php">Mon compte</a>
<a href="formulaire_creation_annonces.php">Déposer une annonce</a>
<a href="deconnexion.php">Déconnexion</a>
```

## `deconnexion.php`

```php
session_start();
session_unset();
session_destroy();
header('Location: page_annonces.php');
```

Détruit la session → retour visiteur (nav avec « Connexion » uniquement).

## Pages protégées et placeholders

| Fichier | Accès | Statut |
|---------|-------|--------|
| `messages.php` | Connecté requis | Messagerie William intégrée |
| `envoyer.php` | Connecté requis (POST) | Traitement envoi message |
| `mon_compte.php` | Connecté requis | Affiche « Bonjour Prénom Nom » depuis la session |
| `inscription_blogmoteur.php` | Public | Inscription + connexion auto |
| `formulaire_modification_annonces.php` | — | F4 modification — à développer |

## Test du parcours connexion + nav

1. Ouvrir `page_annonces.php` → nav : **Annonces | Connexion**
2. Se connecter (`alice.test@blogmoteur.local` / `password`)
3. Redirection → **même page** → nav complète
4. Cliquer **Déconnexion** → nav redevient visiteur

---

# Messagerie — `messages.php` + `envoyer.php` (William)

> **Accès :** utilisateur connecté uniquement.  
> **Table :** `messages` (`id_anno`, `id_expediteur`, `id_destinataire`, `contenu`, `created_at`).

## Fichiers William intégrés puis supprimés

| Fichier d’origine | Remplacé par |
|-------------------|--------------|
| `messagerie_william.php` | `messages.php` (interface complète) |
| `fenetre_william.php` | Formulaire intégré dans `messages.php` |
| `envoyer_william.php` (incomplet) | `envoyer.php` (INSERT + redirect) |

## Flux contact vendeur

1. Visiteur sur `detail_annonce.php` → **Contactez-nous**
2. `connexion.php?redirect=messages&id_anno=X`
3. Après login → `messages.php?id_anno=X` (le vendeur = `annonces.id_user`)
4. Saisie du message → POST `envoyer.php`
5. `INSERT` en base → redirection `messages.php?id_anno=X&id_destinataire=Y`

## `messages.php` — structure

| Zone | Rôle |
|------|------|
| Sidebar | Liste des interlocuteurs (requête sur table `messages` + `users`) |
| En-tête chat | Nom du vendeur + titre de l’annonce |
| Fil messages | Bulles bleues (moi) / blanches (autre), filtrées par `id_anno` + paire d’utilisateurs |
| Formulaire | `contenu` + champs cachés `id_anno`, `id_destinataire` |

## `envoyer.php` — sécurité

- `id_expediteur` pris depuis **`$_SESSION['id_user']`** (jamais depuis le POST)
- `INSERT` avec requête **préparée** (`mysqli_prepare`)
- `htmlspecialchars` à l’affichage des messages dans `messages.php`
- Impossible de s’envoyer un message à soi-même

## Test messagerie

1. Créer un 2e compte via `inscription_blogmoteur.php`
2. Se connecter avec ce compte
3. Ouvrir une annonce d’Alice → **Contactez-nous** (ou via connexion si visiteur)
4. Envoyer un message → vérifier affichage dans la conversation
5. Se connecter en Alice → **Messages** → voir la conversation

---

# Création d’annonce — `formulaire_creation_annonces.php` (F3)

> **Accès :** utilisateur connecté uniquement (`$_SESSION['id_user']`).  
> **Traitement :** sur la **même page** (formulaire + PHP en haut du fichier).

## Structure du fichier

```
Lignes 1-7     → session + redirection si non connecté
Lignes 9-39    → connexion BDD, chargement catégories
Lignes 41-132  → traitement POST (validation, INSERT, upload photo)
Lignes 138-140 → fonction champ_val()
Lignes 142+    → HTML : header, formulaire, footer
```

## Champs du formulaire

| Champ | Obligatoire | Exemple placeholder |
|-------|-------------|----------------------|
| Catégorie | Oui | Voitures / Motos / Utilitaires |
| Titre | Oui | Peugeot 208 — très bon état |
| Prix | Oui | 9500 |
| Description | Oui | Entretien à jour… |
| Marque | Oui | Peugeot |
| Modèle | Oui | 208 |
| Année | Oui | 2019 |
| Kilométrage | Oui | 62000 |
| Carburant | Oui | essence / diesel / electrique |
| Boîte | Oui | manuelle / automatique |
| État | Oui | occasion / neuf |
| Localisation | Oui | 75011 Paris |
| Téléphone | Non | 0600000000 |
| Photo | Oui | fichier local (jpg, png, webp) |

Le formulaire utilise `enctype="multipart/form-data"` pour l’upload de la photo.

## Validation côté serveur

Avant insertion, on vérifie :
- champs obligatoires non vides ;
- `prix > 0`, `annee > 0`, `kilometrage >= 0` ;
- photo présente et extension autorisée (`jpg`, `jpeg`, `png`, `webp`).

En cas d’erreur → message rouge + **formulaire pré-rempli** via `$valeurs` et `champ_val()`.

## INSERT annonce — requête préparée

```php
$sql = 'INSERT INTO annonces (
            id_user, id_cat, titre, prix, description,
            marque, modele, annee, kilometrage,
            carburant, boite_vitesse, etat, localisation, telephone
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'iisdsssiissss', $id_user, $id_cat, ...);
mysqli_stmt_execute($stmt);
$id_anno = mysqli_insert_id($conn);
```

| Type `bind_param` | Champ |
|-------------------|-------|
| `i` | id_user, id_cat, annee, kilometrage |
| `d` | prix (décimal) |
| `s` | titre, description, marque, modèle, carburant, boîte, état, localisation, téléphone |

**Sécurité soutenance :** aucune valeur `$_POST` n’est concaténée dans le SQL.

## Upload photo

```php
move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $nom_fichier);
$chemin = '/blogmoteur_annonces/uploads/' . $nom_fichier;
```

Puis INSERT préparé dans `photos` :

```sql
INSERT INTO photos (id_anno, chemin, ordre) VALUES (?, ?, 1)
```

Si l’upload échoue → l’annonce insérée est **supprimée** (pas d’annonce sans photo).

## Après succès

```php
header('Location: detail_annonce.php?id_anno=' . $id_anno);
```

L’utilisateur voit directement sa nouvelle annonce.

## Boutons

| Bouton | Action |
|--------|--------|
| **Valider** | `POST` name=`valider` → traitement + insertion |
| **Annuler** | Lien vers `page_annonces.php` (aucune modification) |

## Styles (`.form-annonce` dans `site.css`)

Carte blanche comme les filtres Omar : labels, champs en grille 2 colonnes, boutons bleu (valider) et gris (annuler).

## Résumé oral F3

> « Le formulaire envoie les données en POST sur la même page. On valide les champs, on insère l’annonce avec une requête préparée, on upload la photo dans `uploads/`, on enregistre le chemin en base, puis on redirige vers la fiche détail. »

---

# Modifications courantes

| Tu veux… | Fichier | Où modifier |
|----------|---------|-------------|
| Ajouter un filtre (ex. marque) | `form-filtres.php` + `filtres-where.php` | Nouveau champ + nouvelle condition `AND` |
| Changer le tri de la liste | `page_annonces.php` | `ORDER BY a.created_at DESC` |
| Ajouter un lien dans la nav connectée | `includes/header.php` | Bloc `if ($utilisateur_connecte)` |
| Masquer les vues au public | `detail_annonce.php` | Lignes `<dt>Vues</dt>` |
| Inscription sécurisée | `inscription_blogmoteur.php` | `password_hash` + requête préparée comme connexion |
| Contacter un vendeur | `detail_annonce.php` → `connexion.php` → `messages.php` | Bouton visiteur + redirect après login |
| Modifier une annonce (F4) | `formulaire_modification_annonces.php` | À créer sur le modèle de la création |
| Retirer un filtre actif | URL | Lien « Effacer les filtres » ou supprimer les `?` dans l’URL |
| Changer les champs du formulaire création | `formulaire_creation_annonces.php` | HTML + validation + `bind_param` |

---

# Résumé oral pour le prof

> **F6 (Michè)** : liste en cartes avec un `SELECT` qui joint `categories` et la première `photo`.  
> **F7 (Michè)** : fiche détail via `id_anno` en GET ; bonus vues avec `UPDATE` + session anti-F5.  
> **F8 (Omar)** : formulaire GET + `filtres-where.php` qui construit des `AND` injectés dans `WHERE 1=1` de la liste.  
> **F9 (Omar)** : bouton POST vers `toggle-favori.php` (INSERT/DELETE) ; page `favoris.php` liste mes favoris avec jointures SQL.  
> **F2 (connexion)** : requête préparée (`mysqli_prepare`) + `password_verify` + `htmlspecialchars` — pas de SQL concaténé.  
> **F3 (création)** : formulaire + traitement même page ; INSERT préparé + upload photo + INSERT `photos`.  
> **Messagerie (William)** : `messages.php` affiche les conversations ; `envoyer.php` insère en base avec requête préparée ; contact vendeur depuis la fiche détail.  
> **Nav dynamique** : `includes/header.php` selon `$_SESSION['id_user']` ; après login, même liste avec nav complète.  
> **Intégration** : includes Omar dans les pages Michè ; messagerie William dans `messages.php` ; layout commun `header.php` / `footer.php` / `site.css`.
