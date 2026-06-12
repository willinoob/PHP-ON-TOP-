<?php
// F8 (Omar) — formulaire de filtrage (nécessite $conn ouvert par page_annonces.php)

$categorie_selectionnee = isset($_GET['categorie']) ? (int) $_GET['categorie'] : 0;
$prix_max_selectionne = isset($_GET['prix_max']) ? htmlspecialchars($_GET['prix_max'], ENT_QUOTES, 'UTF-8') : '';
$filtres_actifs = $categorie_selectionnee > 0 || $prix_max_selectionne !== '';
?>

<div class="filtres-bloc">
    <form class="filtres-form" action="page_annonces.php" method="get">
        <h2 class="filtres-form__titre">Filtrer les annonces</h2>

        <div class="filtres-form__champs">
            <div class="filtres-form__groupe">
                <label for="categorie">Catégorie</label>
                <select name="categorie" id="categorie">
                    <option value="">Toutes les catégories</option>
                    <?php
                    $req_cats = mysqli_query($conn, 'SELECT id_cat, nom FROM categories ORDER BY nom ASC');
                    while ($ligne = mysqli_fetch_assoc($req_cats)) {
                        $id_cat = (int) $ligne['id_cat'];
                        $selected = $id_cat === $categorie_selectionnee ? ' selected' : '';
                        echo '<option value="' . $id_cat . '"' . $selected . '>'
                            . htmlspecialchars($ligne['nom']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="filtres-form__groupe">
                <label for="prix_max">Prix maximum (€)</label>
                <input type="number" name="prix_max" id="prix_max" min="0" step="1"
                       value="<?= $prix_max_selectionne ?>" placeholder="Ex : 15000">
            </div>
        </div>

        <div class="filtres-form__actions">
            <button type="submit" class="filtres-form__btn filtres-form__btn--filtrer">Filtrer</button>
            <?php if ($filtres_actifs): ?>
                <a href="page_annonces.php" class="filtres-form__btn filtres-form__btn--effacer">Effacer les filtres</a>
            <?php endif; ?>
        </div>
    </form>
</div>
