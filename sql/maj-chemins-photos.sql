-- Après avoir copié les images dans blogmoteur-cours/uploads/
USE bd_blogmoteur;

UPDATE photos p
INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = '/blogmoteur-cours/uploads/peugeot_208.jpg'
WHERE a.titre = 'Peugeot 208 — très bon état';

UPDATE photos p
INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = '/blogmoteur-cours/uploads/yamaha_M07.jpg'
WHERE a.titre = 'Yamaha MT-07';

UPDATE photos p
INNER JOIN annonces a ON a.id_anno = p.id_anno AND p.ordre = 1
SET p.chemin = '/blogmoteur-cours/uploads/renault_trafic.jpg'
WHERE a.titre = 'Renault Trafic utilitaire';
