-- Corrige les chemins photos en base → format relatif uploads/nom.jpg
-- À exécuter dans phpMyAdmin si les images s'affichent en blanc chez un coéquipier.
-- William : lance ce script après avoir copié le dossier uploads/ depuis Git.

USE bd_blogmoteur;

UPDATE photos
SET chemin = CONCAT('uploads/', SUBSTRING_INDEX(chemin, '/', -1));
