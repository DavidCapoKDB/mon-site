<?php
// Inclure le fichier de connexion à la base de données
require_once 'include/bdd.php'; // Connexion à la base de données avec la variable $conn
include "header.php" ;
// Traitement de la suppression d'un produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimer') {
    $idProduit = intval($_POST['idProduit']);
    $sql = "DELETE FROM produits WHERE idProduit = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idProduit);
    $stmt->execute();
    $stmt->close();
}

// Traitement de la modification d'un produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifier') {
    $idProduit = intval($_POST['idProduit']);
    $nom = $_POST['nom'];
    $categorie = $_POST['categorie'];
    $description = $_POST['description'];
    $prix = floatval($_POST['prix']);
    
    $sql = "UPDATE produits SET nom = ?, categorie = ?, description = ?, prix = ? WHERE idProduit = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdi", $nom, $categorie, $description, $prix, $idProduit);
    $stmt->execute();
    $stmt->close();
}

// Récupérer tous les produits au chargement initial
$sql = "SELECT * FROM produits";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Produits</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="accueilVendeur.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle"></i> Retour
        </a>
        <div class="input-group w-50">
            <input type="text" id="search" class="form-control" placeholder="Rechercher un produit...">
        </div>
    </div>
    
    <div id="product-list" class="row">
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= htmlspecialchars($row['image']); ?>" class="card-img-top" alt="Image du produit">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['nom']); ?></h5>
                        <p class="card-text"><strong>Catégorie:</strong> <?= htmlspecialchars($row['categorie']); ?></p>
                        <p class="card-text"><?= htmlspecialchars($row['description']); ?></p>
                        <p class="card-text"><strong>Prix:</strong> <?= htmlspecialchars($row['prix']); ?> €</p>
                    </div>
                    <div class="card-footer">
                        <!-- Formulaire de modification -->
                        <form method="post" class="d-inline">
                            <input type="hidden" name="action" value="modifier">
                            <input type="hidden" name="idProduit" value="<?= $row['idProduit']; ?>">
                            <button type="button" class="btn btn-primary btn-sm" onclick="editProduct(this)">Modifier</button>
                        </form>
                        <!-- Formulaire de suppression -->
                        <form method="post" class="d-inline" onsubmit="return confirmDelete();">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="idProduit" value="<?= $row['idProduit']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Modale pour la modification de produit -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Modifier le produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" method="post">
                    <input type="hidden" name="action" value="modifier">
                    <input type="hidden" id="editProductId" name="idProduit">
                    <div class="mb-3">
                        <label for="editProductName" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="editProductName" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProductCategory" class="form-label">Catégorie</label>
                        <input type="text" class="form-control" id="editProductCategory" name="categorie" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProductDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editProductDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editProductPrice" class="form-label">Prix</label>
                        <input type="number" step="0.01" class="form-control" id="editProductPrice" name="prix" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Fonction de confirmation de suppression
function confirmDelete() {
    return confirm("Êtes-vous sûr de vouloir supprimer ce produit ?");
}

// Fonction de modification de produit
function editProduct(button) {
    var card = button.closest('.card');
    var nom = card.querySelector('.card-title').innerText;
    var categorie = card.querySelector('.card-text strong:nth-child(1)').innerText.split(': ')[1];
    var description = card.querySelector('.card-text:nth-of-type(2)').innerText;
    var prix = card.querySelector('.card-text:nth-of-type(3)').innerText.split(': ')[1].replace('€', '').trim();
    var idProduit = button.closest('form').querySelector('input[name="idProduit"]').value;

    document.getElementById('editProductId').value = idProduit;
    document.getElementById('editProductName').value = nom;
    document.getElementById('editProductCategory').value = categorie;
    document.getElementById('editProductDescription').value = description;
    document.getElementById('editProductPrice').value = prix;

    var modal = new bootstrap.Modal(document.getElementById('editProductModal'));
    modal.show();
}

// AJAX pour la recherche sans rechargement de page
$(document).ready(function() {
    $('#search').on('keyup', function() {
        var searchValue = $(this).val();
        $.ajax({
            url: 'rechercher_produit.php',
            method: 'GET',
            data: { search: searchValue },
            success: function(response) {
                $('#product-list').html(response);
            }
        });
    });

    // Initial load of products
    $.ajax({
        url: 'rechercher_produit.php',
        method: 'GET',
        success: function(response) {
            $('#product-list').html(response);
        }
    });
});
</script>
</body>
</html>
