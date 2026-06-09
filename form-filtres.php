<?php
// Omar — F8 : formulaire de filtrage

// On garde en mémoire les choix de l'utilisateur pour pré-remplir les champs
$filtre_cat = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
$filtre_prix_max = isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? (float)$_GET['prix_max'] : null;
$filtre_km_max = isset($_GET['km_max']) && $_GET['km_max'] !== '' ? (int)$_GET['km_max'] : null;
$filtre_carburant = isset($_GET['carburant']) ? $_GET['carburant'] : '';

// Récupération des catégories pour le menu déroulant
$res_cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY nom ASC");
?>

<style>
    .zone-filtres { background: #fff; padding: 1.5rem; border-radius: 10px; border: 1px solid #e0ddd5; margin-bottom: 2rem; }
    .form-filtres { display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; }
    .groupe-filtre { display: flex; flex-direction: column; gap: 0.25rem; }
    .groupe-filtre label { font-size: 0.85rem; color: #555; font-weight: 600; }
    .groupe-filtre select, .groupe-filtre input { padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px; min-width: 160px; font-size: 0.9rem; }
    .btn-filtrer { background: #2a5bc4; color: white; border: none; padding: 0.5rem 1.5rem; border-radius: 6px; cursor: pointer; font-weight: 600; height: 38px; }
    .btn-filtrer:hover { background: #1e4294; }
    .btn-reset { background: #e4e4e7; color: #333; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; text-decoration: none; text-align: center; height: 38px; display: inline-flex; align-items: center; font-size: 0.9rem; box-sizing: border-box; }
</style>

<div class="zone-filtres">
    <form method="GET" action="page_annonces.php" class="form-filtres">
        
        <div class="groupe-filtre">
            <label for="categorie">Catégorie</label>
            <select name="categorie" id="categorie">
                <option value="">Toutes les catégories</option>
                <?php while($cat = mysqli_fetch_assoc($res_cats)): ?>
                    <option value="<?= $cat['id_cat'] ?>" <?= $filtre_cat === (int)$cat['id_cat'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="groupe-filtre">
            <label for="prix_max">Prix maximum (€)</label>
            <input type="number" name="prix_max" id="prix_max" min="0" value="<?= $filtre_prix_max !== null ? $filtre_prix_max : '' ?>" placeholder="Ex: 15000">
        </div>

        <div class="groupe-filtre">
            <label for="km_max">Kilométrage max</label>
            <input type="number" name="km_max" id="km_max" min="0" value="<?= $filtre_km_max !== null ? $filtre_km_max : '' ?>" placeholder="Ex: 50000">
        </div>

        <div class="groupe-filtre">
            <label for="carburant">Carburant</label>
            <select name="carburant" id="carburant">
                <option value="">Tous</option>
                <option value="essence" <?= $filtre_carburant === 'essence' ? 'selected' : '' ?>>Essence</option>
                <option value="diesel" <?= $filtre_carburant === 'diesel' ? 'selected' : '' ?>>Diesel</option>
                <option value="electrique" <?= $filtre_carburant === 'electrique' ? 'selected' : '' ?>>Électrique</option>
            </select>
        </div>

        <button type="submit" class="btn-filtrer">Filtrer</button>
        
        <?php if($filtre_cat || $filtre_prix_max || $filtre_km_max || $filtre_carburant): ?>
            <a href="page_annonces.php" class="btn-reset">Effacer les filtres</a>
        <?php endif; ?>
    </form>
</div>