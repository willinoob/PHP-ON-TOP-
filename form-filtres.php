<?php
// Connexion à la base de données standard (identique au cours)
$id = mysqli_connect('localhost', 'root', '', 'bd_blogmoteur');

// On garde en mémoire les choix de l'utilisateur
$categorie_selectionnee = isset($_GET['categorie']) ? $_GET['categorie'] : '';
$carburant_selectionne = isset($_GET['carburant']) ? $_GET['carburant'] : '';
?>

<div class="container-filtres">
    <form action="" method="get">

        <div class="champ-filtre">
            <label for="categorie">Catégorie</label>
            <select name="categorie" id="categorie">
                <option value="">Toutes</option>
                <?php
                $req_cats = 'SELECT * FROM categories ORDER BY nom ASC';
                $res_cats = mysqli_query($id, $req_cats);
                while ($ligne = mysqli_fetch_assoc($res_cats)) {
                    $selected = ($ligne['id_cat'] == $categorie_selectionnee) ? 'selected' : '';
                    echo "<option value='" . $ligne['id_cat'] . "' " . $selected . '>' . htmlspecialchars($ligne['nom']) . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="champ-filtre">
            <label for="marque">Marque</label>
            <input type="text" name="marque" id="marque" value="<?php if (isset($_GET['marque'])) {
                echo htmlspecialchars($_GET['marque']);
            } ?>" placeholder="Ex: Peugeot">
        </div>

        <div class="champ-filtre">
            <label for="prix_max">Prix max</label>
            <input type="number" name="prix_max" id="prix_max" value="<?php if (isset($_GET['prix_max'])) {
                echo htmlspecialchars($_GET['prix_max']);
            } ?>" placeholder="En €">
        </div>

        <div class="champ-filtre">
            <label for="km_max">KM Max</label>
            <input type="number" name="km_max" id="km_max" value="<?php if (isset($_GET['km_max'])) {
                echo htmlspecialchars($_GET['km_max']);
            } ?>" placeholder="Ex: 80000">
        </div>

        <div class="champ-filtre">
            <label for="carburant">Carburant</label>
            <select name="carburant" id="carburant">
                <option value="">Tous</option>
                <option value="Essence" <?php if ($carburant_selectionne == 'Essence') {
                    echo 'selected';
                } ?>>Essence</option>
                <option value="Diesel" <?php if ($carburant_selectionne == 'Diesel') {
                    echo 'selected';
                } ?>>Diesel</option>
                <option value="Electrique" <?php if ($carburant_selectionne == 'Electrique') {
                    echo 'selected';
                } ?>>Électrique</option>
                <option value="Hybride" <?php if ($carburant_selectionne == 'Hybride') {
                    echo 'selected';
                } ?>>Hybride</option>
            </select>
        </div>

        <div class="champ-filtre">
            <label for="ville">Ville</label>
            <input type="text" name="ville" id="ville" value="<?php if (isset($_GET['ville'])) {
                echo htmlspecialchars($_GET['ville']);
            } ?>" placeholder="Ex: Paris">
        </div>

        <button type="submit" class="btn-recherche">🔍</button>

        <?php if (!empty($_GET['categorie']) || !empty($_GET['marque']) || !empty($_GET['prix_max']) || !empty($_GET['km_max']) || !empty($_GET['carburant']) || !empty($_GET['ville'])): ?>
            <a href="page_annonces.php" class="btn-annuler">❌</a>
        <?php endif; ?>

    </form>
</div>

<?php
mysqli_close($id);
?>
