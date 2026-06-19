-- 10 annonces + photos (version simplifiée Michè)
-- phpMyAdmin → bd_blogmoteur → SQL → Exécuter

USE bd_blogmoteur;

SET @uid = (SELECT id_user FROM users WHERE email = 'alice.test@blogmoteur.local' LIMIT 1);
SET @uid = IFNULL(@uid, 1);
SET @base = 'uploads/';

-- ========== 3 annonces d'origine — chemins photos ==========
UPDATE photos p
INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'peugeot_208.jpg')
WHERE a.titre = 'Peugeot 208 — très bon état';

UPDATE photos p
INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'yamaha_M07.jpg')
WHERE a.titre = 'Yamaha MT-07';

UPDATE photos p
INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'renault_trafic.jpg')
WHERE a.titre = 'Renault Trafic utilitaire';

-- ========== VOITURES (5) ==========
INSERT INTO annonces (id_user, id_cat, titre, prix, description, marque, modele, annee, kilometrage, carburant, boite_vitesse, etat, couleur, localisation, telephone)
SELECT @uid, 1, 'Renault Clio V TCe 90', 11200.00, 'Citadine économique, faible consommation.', 'Renault', 'Clio V', 2021, 45000, 'essence', 'manuelle', 'occasion', 'Rouge', '31000 Toulouse', '0611111101'
WHERE NOT EXISTS (SELECT 1 FROM annonces WHERE titre = 'Renault Clio V TCe 90');

INSERT INTO annonces (id_user, id_cat, titre, prix, description, marque, modele, annee, kilometrage, carburant, boite_vitesse, etat, couleur, localisation, telephone)
SELECT @uid, 1, 'Volkswagen Golf 8 TDI', 18900.00, 'Finition Confortline, GPS.', 'Volkswagen', 'Golf 8', 2020, 78000, 'diesel', 'automatique', 'occasion', 'Gris', '59000 Lille', NULL
WHERE NOT EXISTS (SELECT 1 FROM annonces WHERE titre = 'Volkswagen Golf 8 TDI');

INSERT INTO annonces (id_user, id_cat, titre, prix, description, marque, modele, annee, kilometrage, carburant, boite_vitesse, etat, couleur, localisation, telephone)
SELECT @uid, 1, 'Tesla Model 3 Standard', 28500.00, '100% électrique, autonomie confortable.', 'Tesla', 'Model 3', 2019, 62000, 'electrique', 'automatique', 'occasion', 'Blanc', '75015 Paris', '0622222202'
WHERE NOT EXISTS (SELECT 1 FROM annonces WHERE titre = 'Tesla Model 3 Standard');

INSERT INTO annonces (id_user, id_cat, titre, prix, description, marque, modele, annee, kilometrage, carburant, boite_vitesse, etat, couleur, localisation, telephone)
SELECT @uid, 1, 'Citroën C3 Aircross', 13400.00, 'SUV compact, parfait famille.', 'Citroën', 'C3 Aircross', 2018, 89000, 'essence', 'manuelle', 'occasion', 'Orange', '44000 Nantes', NULL
WHERE NOT EXISTS (SELECT 1 FROM annonces WHERE titre = 'Citroën C3 Aircross');

INSERT INTO annonces (id_user, id_cat, titre, prix, description, marque, modele, annee, kilometrage, carburant, boite_vitesse, etat, couleur, localisation, telephone)
SELECT @uid, 1, 'BMW Série 1 118d', 16500.00, 'Boîte auto, sellerie cuir.', 'BMW', 'Série 1', 2017, 105000, 'diesel', 'automatique', 'occasion', 'Noir', '69001 Lyon', '0633333303'
WHERE NOT EXISTS (SELECT 1 FROM annonces WHERE titre = 'BMW Série 1 118d');

-- ========== MOTOS (2) ==========
INSERT INTO annonces (id_user, id_cat, titre, prix, description, marque, modele, annee, kilometrage, carburant, boite_vitesse, etat, couleur, localisation, telephone)
SELECT @uid, 2, 'Honda CB500F', 5200.00, 'Roadster légère et maniable.', 'Honda', 'CB500F', 2019, 22000, 'essence', 'manuelle', 'occasion', 'Bleu', '13001 Marseille', NULL
WHERE NOT EXISTS (SELECT 1 FROM annonces WHERE titre = 'Honda CB500F');

INSERT INTO annonces (id_user, id_cat, titre, prix, description, marque, modele, annee, kilometrage, carburant, boite_vitesse, etat, couleur, localisation, telephone)
SELECT @uid, 2, 'Kawasaki Z650', 5800.00, 'Freins récents, prête à rouler.', 'Kawasaki', 'Z650', 2018, 31000, 'essence', 'manuelle', 'occasion', 'Vert', '67000 Strasbourg', '0644444404'
WHERE NOT EXISTS (SELECT 1 FROM annonces WHERE titre = 'Kawasaki Z650');

-- ========== UTILITAIRES (3) ==========
INSERT INTO annonces (id_user, id_cat, titre, prix, description, marque, modele, annee, kilometrage, carburant, boite_vitesse, etat, couleur, localisation, telephone)
SELECT @uid, 3, 'Ford Transit Custom L2H1', 16800.00, 'Aménagement artisan, hayon arrière.', 'Ford', 'Transit Custom', 2019, 120000, 'diesel', 'manuelle', 'occasion', 'Blanc', '35000 Rennes', '0677777707'
WHERE NOT EXISTS (SELECT 1 FROM annonces WHERE titre = 'Ford Transit Custom L2H1');

INSERT INTO annonces (id_user, id_cat, titre, prix, description, marque, modele, annee, kilometrage, carburant, boite_vitesse, etat, couleur, localisation, telephone)
SELECT @uid, 3, 'Mercedes Sprinter 314 CDI', 22400.00, 'Utilitaire pro, grande cabine.', 'Mercedes', 'Sprinter', 2018, 145000, 'diesel', 'manuelle', 'occasion', 'Gris', '59000 Roubaix', NULL
WHERE NOT EXISTS (SELECT 1 FROM annonces WHERE titre = 'Mercedes Sprinter 314 CDI');

INSERT INTO annonces (id_user, id_cat, titre, prix, description, marque, modele, annee, kilometrage, carburant, boite_vitesse, etat, couleur, localisation, telephone)
SELECT @uid, 3, 'Iveco Daily 35S14', 19500.00, 'PTAC élevé, hayon élévateur.', 'Iveco', 'Daily', 2016, 180000, 'diesel', 'manuelle', 'occasion', 'Blanc', '69007 Lyon', '0688888808'
WHERE NOT EXISTS (SELECT 1 FROM annonces WHERE titre = 'Iveco Daily 35S14');

-- ========== PHOTOS — INSERT si manquante ==========
INSERT INTO photos (id_anno, chemin, ordre)
SELECT a.id_anno, CONCAT(@base, 'Renault Clio V TCe 90.jpg'), 1 FROM annonces a WHERE a.titre = 'Renault Clio V TCe 90'
AND NOT EXISTS (SELECT 1 FROM photos p WHERE p.id_anno = a.id_anno AND p.ordre = 1);

INSERT INTO photos (id_anno, chemin, ordre)
SELECT a.id_anno, CONCAT(@base, 'Volkswagen Golf 8 TDI.jpg'), 1 FROM annonces a WHERE a.titre = 'Volkswagen Golf 8 TDI'
AND NOT EXISTS (SELECT 1 FROM photos p WHERE p.id_anno = a.id_anno AND p.ordre = 1);

INSERT INTO photos (id_anno, chemin, ordre)
SELECT a.id_anno, CONCAT(@base, 'Tesla Model 3 Standard.jpg'), 1 FROM annonces a WHERE a.titre = 'Tesla Model 3 Standard'
AND NOT EXISTS (SELECT 1 FROM photos p WHERE p.id_anno = a.id_anno AND p.ordre = 1);

INSERT INTO photos (id_anno, chemin, ordre)
SELECT a.id_anno, CONCAT(@base, 'Citroën C3 Aircross.jpg'), 1 FROM annonces a WHERE a.titre = 'Citroën C3 Aircross'
AND NOT EXISTS (SELECT 1 FROM photos p WHERE p.id_anno = a.id_anno AND p.ordre = 1);

INSERT INTO photos (id_anno, chemin, ordre)
SELECT a.id_anno, CONCAT(@base, 'BMW Série 1 118d.jpg'), 1 FROM annonces a WHERE a.titre = 'BMW Série 1 118d'
AND NOT EXISTS (SELECT 1 FROM photos p WHERE p.id_anno = a.id_anno AND p.ordre = 1);

INSERT INTO photos (id_anno, chemin, ordre)
SELECT a.id_anno, CONCAT(@base, 'Honda CB500F.jpg'), 1 FROM annonces a WHERE a.titre = 'Honda CB500F'
AND NOT EXISTS (SELECT 1 FROM photos p WHERE p.id_anno = a.id_anno AND p.ordre = 1);

INSERT INTO photos (id_anno, chemin, ordre)
SELECT a.id_anno, CONCAT(@base, 'Kawasaki Z650.jpg'), 1 FROM annonces a WHERE a.titre = 'Kawasaki Z650'
AND NOT EXISTS (SELECT 1 FROM photos p WHERE p.id_anno = a.id_anno AND p.ordre = 1);

INSERT INTO photos (id_anno, chemin, ordre)
SELECT a.id_anno, CONCAT(@base, 'Ford Transit Custom L2H1.jpg'), 1 FROM annonces a WHERE a.titre = 'Ford Transit Custom L2H1'
AND NOT EXISTS (SELECT 1 FROM photos p WHERE p.id_anno = a.id_anno AND p.ordre = 1);

INSERT INTO photos (id_anno, chemin, ordre)
SELECT a.id_anno, CONCAT(@base, 'Mercedes Sprinter 314 CDI.jpg'), 1 FROM annonces a WHERE a.titre = 'Mercedes Sprinter 314 CDI'
AND NOT EXISTS (SELECT 1 FROM photos p WHERE p.id_anno = a.id_anno AND p.ordre = 1);

INSERT INTO photos (id_anno, chemin, ordre)
SELECT a.id_anno, CONCAT(@base, 'Iveco Daily 35S14.jpg'), 1 FROM annonces a WHERE a.titre = 'Iveco Daily 35S14'
AND NOT EXISTS (SELECT 1 FROM photos p WHERE p.id_anno = a.id_anno AND p.ordre = 1);

-- ========== PHOTOS — UPDATE (si annonces/photos existent déjà) ==========
UPDATE photos p INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'Renault Clio V TCe 90.jpg') WHERE a.titre = 'Renault Clio V TCe 90';

UPDATE photos p INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'Volkswagen Golf 8 TDI.jpg') WHERE a.titre = 'Volkswagen Golf 8 TDI';

UPDATE photos p INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'Tesla Model 3 Standard.jpg') WHERE a.titre = 'Tesla Model 3 Standard';

UPDATE photos p INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'Citroën C3 Aircross.jpg') WHERE a.titre = 'Citroën C3 Aircross';

UPDATE photos p INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'BMW Série 1 118d.jpg') WHERE a.titre = 'BMW Série 1 118d';

UPDATE photos p INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'Honda CB500F.jpg') WHERE a.titre = 'Honda CB500F';

UPDATE photos p INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'Kawasaki Z650.jpg') WHERE a.titre = 'Kawasaki Z650';

UPDATE photos p INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'Ford Transit Custom L2H1.jpg') WHERE a.titre = 'Ford Transit Custom L2H1';

UPDATE photos p INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'Mercedes Sprinter 314 CDI.jpg') WHERE a.titre = 'Mercedes Sprinter 314 CDI';

UPDATE photos p INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = CONCAT(@base, 'Iveco Daily 35S14.jpg') WHERE a.titre = 'Iveco Daily 35S14';
