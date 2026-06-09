-- =============================================================
-- Projet Petites Annonces — bd_blogmoteur.sql
-- MySQL / InnoDB / utf8mb4
-- Base bd_blogmoteur : 6 tables + 3 catégories (pas de données de démo)
-- =============================================================
CREATE DATABASE IF NOT EXISTS bd_blogmoteur
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bd_blogmoteur;
-- Réinitialisation (ordre enfants -> parents pour respecter les FK)
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS favoris;
DROP TABLE IF EXISTS photos;
DROP TABLE IF EXISTS annonces;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- =============================================================
-- users
-- =============================================================
CREATE TABLE users (
    id_user     INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(50)  NOT NULL,
    prenom      VARCHAR(50)  NOT NULL,
    email       VARCHAR(255) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,            -- hash bcrypt (~60 car.)
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- categories (table de référence)
-- =============================================================
CREATE TABLE categories (
    id_cat   INT AUTO_INCREMENT PRIMARY KEY,
    nom  VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- annonces (table centrale)
-- =============================================================
CREATE TABLE annonces (
    id_anno         INT AUTO_INCREMENT PRIMARY KEY,
    id_user        INT NOT NULL,
    id_cat   INT NOT NULL,
    titre          VARCHAR(150) NOT NULL,
    prix           DECIMAL(10,2) NOT NULL,
    description    TEXT NOT NULL,
    marque         VARCHAR(50) NOT NULL,
    modele         VARCHAR(50) NOT NULL,
    annee          SMALLINT NOT NULL,
    kilometrage    INT NOT NULL,
    carburant      VARCHAR(20) NOT NULL,          -- essence/diesel/électrique/hybride
    boite_vitesse  VARCHAR(20) NOT NULL,          -- manuelle/automatique
    etat           VARCHAR(20) NOT NULL,          -- neuf/occasion
    couleur        VARCHAR(30) DEFAULT NULL,      -- optionnel
    localisation   VARCHAR(100) NOT NULL,
    telephone      VARCHAR(20) DEFAULT NULL,      -- optionnel
    vues           INT NOT NULL DEFAULT 0,        -- bonus compteur de vues
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_annonces_user
        FOREIGN KEY (id_user) REFERENCES users(id_user)
        ON DELETE CASCADE,
    CONSTRAINT fk_annonces_categorie
        FOREIGN KEY (id_cat) REFERENCES categories(id_cat)
        ON DELETE RESTRICT          -- on n'autorise pas la suppression d'une catégorie utilisée
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- photos (1 à 3 par annonce — la limite est gérée côté PHP)
-- =============================================================
CREATE TABLE photos (
    id_photo          INT AUTO_INCREMENT PRIMARY KEY,
    id_anno  INT NOT NULL,
    chemin      VARCHAR(255) NOT NULL,
    ordre       TINYINT NOT NULL,                 -- 1 = photo principale
    CONSTRAINT fk_photos_annonce
        FOREIGN KEY (id_anno) REFERENCES annonces(id_anno)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- favoris (liaison users <-> annonces)
-- =============================================================
CREATE TABLE favoris (
    id_fav          INT AUTO_INCREMENT PRIMARY KEY,
    id_user     INT NOT NULL,
    id_anno  INT NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_favoris UNIQUE (id_user, id_anno),   -- pas de doublon
    CONSTRAINT fk_favoris_user
        FOREIGN KEY (id_user) REFERENCES users(id_user)
        ON DELETE CASCADE,
    CONSTRAINT fk_favoris_annonce
        FOREIGN KEY (id_anno) REFERENCES annonces(id_anno)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- messages (échanges au sujet d'une annonce)
-- =============================================================
CREATE TABLE messages (
    id_mess              INT AUTO_INCREMENT PRIMARY KEY,
    id_anno      INT NOT NULL,
    id_expediteur   INT NOT NULL, 
    id_destinataire INT NOT NULL,
    contenu         TEXT NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_messages_annonce
        FOREIGN KEY (id_anno) REFERENCES annonces(id_anno)
        ON DELETE CASCADE,
    CONSTRAINT fk_messages_expediteur
        FOREIGN KEY (id_expediteur) REFERENCES users(id_user)
        ON DELETE CASCADE,
    CONSTRAINT fk_messages_destinataire
        FOREIGN KEY (id_destinataire) REFERENCES users(id_user)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- SEED — catégories (3 lignes figées)
-- =============================================================
INSERT INTO categories (nom) VALUES
    ('Voitures'),
    ('Motos'),
    ('Utilitaires');






