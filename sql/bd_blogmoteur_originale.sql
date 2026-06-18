-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 18 juin 2026 à 16:21
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bd_blogmoteur`
--

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

CREATE TABLE `annonces` (
  `id_anno` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_cat` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `marque` varchar(50) NOT NULL,
  `modele` varchar(50) NOT NULL,
  `annee` smallint(6) NOT NULL,
  `kilometrage` int(11) NOT NULL,
  `carburant` varchar(20) NOT NULL,
  `boite_vitesse` varchar(20) NOT NULL,
  `etat` varchar(20) NOT NULL,
  `couleur` varchar(30) DEFAULT NULL,
  `localisation` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `vues` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id_anno`, `id_user`, `id_cat`, `titre`, `prix`, `description`, `marque`, `modele`, `annee`, `kilometrage`, `carburant`, `boite_vitesse`, `etat`, `couleur`, `localisation`, `telephone`, `vues`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Peugeot 208 — très bon état', 9500.00, 'Entretien à jour, première main, idéal ville.', 'Peugeot', '208', 2019, 62000, 'essence', 'manuelle', 'occasion', 'Gris', '75011 Paris', '0600000001', 18, '2026-06-04 16:23:37', '2026-06-12 13:43:40'),
(2, 1, 2, 'Yamaha MT-07', 6200.00, 'Moto fiable, pneus récents.', 'Yamaha', 'MT-07', 2020, 18000, 'essence', 'manuelle', 'occasion', 'Noir', '69003 Lyon', NULL, 11, '2026-06-04 16:23:37', '2026-06-12 17:59:56'),
(3, 1, 3, 'Renault Trafic utilitaire', 14000.00, 'Grand volume, idéal artisan.', 'Renault', 'Trafic', 2018, 95000, 'diesel', 'manuelle', 'occasion', 'Blanc', '33000 Bordeaux', '0700000002', 7, '2026-06-04 16:23:37', '2026-06-14 18:43:30'),
(4, 1, 1, 'Renault Clio V TCe 90', 11200.00, 'Citadine économique, faible consommation.', 'Renault', 'Clio V', 2021, 45000, 'essence', 'manuelle', 'occasion', 'Rouge', '31000 Toulouse', '0611111101', 18, '2026-06-05 10:18:00', '2026-06-15 14:31:07'),
(5, 1, 1, 'Volkswagen Golf 8 TDI', 18900.00, 'Finition Confortline, GPS.', 'Volkswagen', 'Golf 8', 2020, 78000, 'diesel', 'automatique', 'occasion', 'Gris', '59000 Lille', NULL, 5, '2026-06-05 10:18:00', '2026-06-18 15:30:03'),
(6, 1, 1, 'Tesla Model 3 Standard', 28500.00, '100% électrique, autonomie confortable.', 'Tesla', 'Model 3', 2019, 62000, 'electrique', 'automatique', 'occasion', 'Blanc', '75015 Paris', '0622222202', 5, '2026-06-05 10:18:00', '2026-06-14 18:40:34'),
(7, 1, 1, 'Citroën C3 Aircross', 13400.00, 'SUV compact, parfait famille.', 'Citroën', 'C3 Aircross', 2018, 89000, 'essence', 'manuelle', 'occasion', 'Orange', '44000 Nantes', NULL, 3, '2026-06-05 10:18:00', '2026-06-14 19:01:50'),
(8, 1, 1, 'BMW Série 1 118d', 16500.00, 'Boîte auto, sellerie cuir.', 'BMW', 'Série 1', 2017, 105000, 'diesel', 'automatique', 'occasion', 'Noir', '69001 Lyon', '0633333303', 2, '2026-06-05 10:18:00', '2026-06-14 18:41:02'),
(9, 1, 2, 'Honda CB500F', 5200.00, 'Roadster légère et maniable.', 'Honda', 'CB500F', 2019, 22000, 'essence', 'manuelle', 'occasion', 'Bleu', '13001 Marseille', NULL, 0, '2026-06-05 10:18:00', '2026-06-05 10:18:00'),
(10, 1, 2, 'Kawasaki Z650', 5800.00, 'Freins récents, prête à rouler.', 'Kawasaki', 'Z650', 2018, 31000, 'essence', 'manuelle', 'occasion', 'Vert', '67000 Strasbourg', '0644444404', 2, '2026-06-05 10:18:00', '2026-06-15 13:42:58'),
(11, 1, 3, 'Ford Transit Custom L2H1', 16800.00, 'Aménagement artisan, hayon arrière.', 'Ford', 'Transit Custom', 2019, 120000, 'diesel', 'manuelle', 'occasion', 'Blanc', '35000 Rennes', '0677777707', 1, '2026-06-05 10:18:00', '2026-06-05 16:44:19'),
(12, 1, 3, 'Mercedes Sprinter 314 CDI', 22400.00, 'Utilitaire pro, grande cabine.', 'Mercedes', 'Sprinter', 2018, 145000, 'diesel', 'manuelle', 'occasion', 'Gris', '59000 Roubaix', NULL, 0, '2026-06-05 10:18:00', '2026-06-05 10:18:00'),
(13, 1, 3, 'Iveco Daily 35S14', 19500.00, 'PTAC élevé, hayon élévateur.', 'Iveco', 'Daily', 2016, 180000, 'diesel', 'manuelle', 'occasion', 'Blanc', '69007 Lyon', '0688888808', 32, '2026-06-05 10:18:01', '2026-06-18 15:29:58'),
(14, 2, 2, 'AVATAR', 9000.00, 'AVATAR 😂', '32455', '23423542', 2005, 325434553, 'diesel', 'automatique', 'occasion', NULL, 'PARIS', '234567432455', 7, '2026-06-14 16:54:28', '2026-06-18 15:29:53');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_cat` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_cat`, `nom`) VALUES
(1, 'Voitures'),
(2, 'Motos'),
(3, 'Utilitaires');

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

CREATE TABLE `favoris` (
  `id_fav` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_anno` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `favoris`
--

INSERT INTO `favoris` (`id_fav`, `id_user`, `id_anno`, `created_at`) VALUES
(2, 1, 4, '2026-06-08 15:34:47'),
(5, 2, 13, '2026-06-15 13:42:50');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id_mess` int(11) NOT NULL,
  `id_anno` int(11) NOT NULL,
  `id_expediteur` int(11) NOT NULL,
  `id_destinataire` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id_mess`, `id_anno`, `id_expediteur`, `id_destinataire`, `contenu`, `created_at`) VALUES
(1, 13, 2, 1, 'SALUT JE SUIS INTERESSé PAR VOTRE ANNONCE', '2026-06-10 12:45:09'),
(2, 13, 2, 1, 'GFYFGJ', '2026-06-10 12:46:35'),
(3, 13, 2, 1, 'RYTUYFHJHKGF', '2026-06-10 12:47:15'),
(4, 13, 2, 1, 'TON BABIERE ALICE', '2026-06-15 14:26:24'),
(5, 13, 2, 1, 'va chier', '2026-06-15 14:26:35'),
(6, 4, 2, 1, 'LVDJVHJVKJVH', '2026-06-15 15:13:44'),
(7, 4, 2, 1, 'TON  CON CHIENNE\r\n', '2026-06-15 15:14:00');

-- --------------------------------------------------------

--
-- Structure de la table `photos`
--

CREATE TABLE `photos` (
  `id_photo` int(11) NOT NULL,
  `id_anno` int(11) NOT NULL,
  `chemin` varchar(255) NOT NULL,
  `ordre` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `photos`
--

INSERT INTO `photos` (`id_photo`, `id_anno`, `chemin`, `ordre`) VALUES
(1, 1, '/blogmoteur_annonces/uploads/peugeot_208.jpg', 1),
(2, 2, '/blogmoteur_annonces/uploads/yamaha_M07.jpg', 1),
(3, 3, '/blogmoteur_annonces/uploads/renault_trafic.jpg', 1),
(4, 4, '/blogmoteur_annonces/uploads/Renault Clio V TCe 90.jpg', 1),
(5, 5, '/blogmoteur_annonces/uploads/Volkswagen Golf 8 TDI.jpg', 1),
(6, 6, '/blogmoteur_annonces/uploads/Tesla Model 3 Standard.jpg', 1),
(7, 7, '/blogmoteur_annonces/uploads/Citroën C3 Aircross.jpg', 1),
(8, 8, '/blogmoteur_annonces/uploads/BMW Série 1 118d.jpg', 1),
(9, 9, '/blogmoteur_annonces/uploads/Honda CB500F.jpg', 1),
(10, 10, '/blogmoteur_annonces/uploads/Kawasaki Z650.jpg', 1),
(11, 11, '/blogmoteur_annonces/uploads/Ford Transit Custom L2H1.jpg', 1),
(12, 12, '/blogmoteur_annonces/uploads/Mercedes Sprinter 314 CDI.jpg', 1),
(13, 13, '/blogmoteur_annonces/uploads/Iveco Daily 35S14.jpg', 1),
(14, 14, '/blogmoteur_annonces/uploads/1781448868_pexels-lum3n-44775-406014.jpg', 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `role_user` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `nom`, `prenom`, `email`, `password`, `created_at`, `role_user`) VALUES
(1, 'Dupont', 'Alice', 'alice.test@blogmoteur.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-06-04 16:23:37', 0),
(2, 'bouaffon', 'henri-michel', 'bouaffonmichel8@gmail.com', '$2y$10$Ld6Rjtj0wdK778SPa8WTQusHQUPr//xQQojTrgahwXqCD.1IXSRs.', '2026-06-09 14:54:52', 1),
(3, 'badabo', 'william', 'william@gmail.com', '$2y$10$BFtEANE8aMIzTs5mQE.jL.S43L2sS8CnFWU4UNj1mkGhLHEDhgXxS', '2026-06-15 17:27:35', 1),
(4, 'kandine', 'omar', 'omar@gmail.com', '$2y$10$cUVpLy0690560/2e.fVv2OTr29OgbVPJXiTJ1D25fFTtw8Kn1w38G', '2026-06-15 17:30:02', 1),
(5, 'mehdi', 'mehdi', 'mehdi@gmail.com', '$2y$10$.lwqgTEp4GgOx6sh8Kma0O.3/snLyvSRkoKXQ3MzHJ7ndo.NH7Nzm', '2026-06-15 17:32:16', 1),
(6, 'adon', 'naomie', 'naomieadon216@gmail.com', '$2y$10$pBSOWd5PYn5ee.VGXG7w6eUvcs.4wqZEuYDruAOEEalB86BN86R/i', '2026-06-18 16:07:58', 1),
(7, 'Gamada', 'Osman', 'osmangamada@moteur.com', '$2y$10$auBd8mHJHqblnDfepifz4O06h0Cyyhta70H4Cm2/rUN5uT03QA60q', '2026-06-18 16:13:58', 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id_anno`),
  ADD KEY `fk_annonces_user` (`id_user`),
  ADD KEY `fk_annonces_categorie` (`id_cat`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_cat`);

--
-- Index pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD PRIMARY KEY (`id_fav`),
  ADD UNIQUE KEY `uq_favoris` (`id_user`,`id_anno`),
  ADD KEY `fk_favoris_annonce` (`id_anno`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_mess`),
  ADD KEY `fk_messages_annonce` (`id_anno`),
  ADD KEY `fk_messages_expediteur` (`id_expediteur`),
  ADD KEY `fk_messages_destinataire` (`id_destinataire`);

--
-- Index pour la table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id_photo`),
  ADD KEY `fk_photos_annonce` (`id_anno`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annonces`
--
ALTER TABLE `annonces`
  MODIFY `id_anno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_cat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `favoris`
--
ALTER TABLE `favoris`
  MODIFY `id_fav` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_mess` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `photos`
--
ALTER TABLE `photos`
  MODIFY `id_photo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD CONSTRAINT `fk_annonces_categorie` FOREIGN KEY (`id_cat`) REFERENCES `categories` (`id_cat`),
  ADD CONSTRAINT `fk_annonces_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `fk_favoris_annonce` FOREIGN KEY (`id_anno`) REFERENCES `annonces` (`id_anno`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favoris_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_annonce` FOREIGN KEY (`id_anno`) REFERENCES `annonces` (`id_anno`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_messages_destinataire` FOREIGN KEY (`id_destinataire`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_messages_expediteur` FOREIGN KEY (`id_expediteur`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `fk_photos_annonce` FOREIGN KEY (`id_anno`) REFERENCES `annonces` (`id_anno`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
