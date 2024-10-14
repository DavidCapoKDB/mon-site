<?php
// Inclure le fichier de connexion à la base de données
require_once 'include/bdd.php'; // Connexion à la base de données avec la variable $conn
// Recherche de produits
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM produits WHERE nom LIKE ? OR categorie LIKE ? OR prix LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = '%' . $search . '%';
    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Récupérer tous les produits
    $sql = "SELECT * FROM produits";
    $result = $conn->query($sql);
}

// Générer le HTML pour les produits
while ($row = $result->fetch_assoc()) {
    echo '<div class="col-md-4 mb-4">';
    echo '    <div class="card h-100 shadow-sm">';
    echo '        <img src="' . htmlspecialchars($row['image']) . '" class="card-img-top" alt="Image du produit">';
    echo '        <div class="card-body">';
    echo '            <h5 class="card-title">' . htmlspecialchars($row['nom']) . '</h5>';
    echo '            <p class="card-text"><strong>Catégorie:</strong> ' . htmlspecialchars($row['categorie']) . '</p>';
    echo '            <p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
    echo '            <p class="card-text"><strong>Prix:</strong> ' . htmlspecialchars($row['prix']) . ' €</p>';
    echo '        </div>';
    echo '        <div class="card-footer">';
    echo '            <!-- Formulaire de modification -->';
    echo '            <form method="post" class="d-inline">';
    echo '                <input type="hidden" name="action" value="modifier">';
    echo '                <input type="hidden" name="idProduit" value="' . $row['idProduit'] . '">';
    echo '                <button type="button" class="btn btn-primary btn-sm" onclick="editProduct(this)">Modifier</button>';
    echo '            </form>';
    echo '            <!-- Formulaire de suppression -->';
    echo '            <form method="post" class="d-inline" onsubmit="return confirmDelete();">';
    echo '                <input type="hidden" name="action" value="supprimer">';
    echo '                <input type="hidden" name="idProduit" value="' . $row['idProduit'] . '">';
    echo '                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>';
    echo '            </form>';
    echo '        </div>';
    echo '    </div>';
    echo '</div>';
}
?>
