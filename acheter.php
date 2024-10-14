<?php
session_start();

// Vérifiez si l'utilisateur est connecté et a le rôle de client
if (!isset($_SESSION['user_id']) || $_SESSION['user_function'] !== 'client') {
    header('Location: login.php');
    exit();
}

// Inclure le fichier de connexion à la base de données
include 'include/bdd.php';

// Vérifiez la connexion à la base de données
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer les informations du formulaire
$idProduit = isset($_POST['idProduit']) ? intval($_POST['idProduit']) : 0;
$quantite = isset($_POST['quantite']) ? intval($_POST['quantite']) : 0;
$idClient = $_SESSION['user_id']; // ID du client depuis la session

if ($idProduit > 0 && $quantite > 0) {
    // Commencer une transaction
    $conn->begin_transaction();
    
    try {
        // Préparer la requête SQL pour insérer une nouvelle commande
        $sqlCommande = "INSERT INTO commande (idClient, dateCommande) VALUES (?, NOW())";
        $stmtCommande = $conn->prepare($sqlCommande);
        if ($stmtCommande === false) {
            throw new Exception("Erreur de préparation de la requête de commande : " . $conn->error);
        }
        $stmtCommande->bind_param("i", $idClient);
        $stmtCommande->execute();
        $idCommande = $stmtCommande->insert_id; // Obtenir l'ID de la commande insérée

        // Préparer la requête SQL pour insérer les détails de la commande
        $sqlDetails = "INSERT INTO detailsCommande (idCommande, idProduit, quantite) VALUES (?, ?, ?)";
        $stmtDetails = $conn->prepare($sqlDetails);
        if ($stmtDetails === false) {
            throw new Exception("Erreur de préparation de la requête des détails de commande : " . $conn->error);
        }
        $stmtDetails->bind_param("iii", $idCommande, $idProduit, $quantite);
        $stmtDetails->execute();

        // Commit la transaction
        $conn->commit();

        // Message de succès
        echo "<p class='text-success'>Commande effectuée avec succès !</p>";
    } catch (Exception $e) {
        // Rollback la transaction en cas d'erreur
        $conn->rollback();
        // Message d'erreur
        echo "<p class='text-danger'>Erreur lors de la commande : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='text-danger'>Quantité invalide ou produit non spécifié.</p>";
}

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acheter Produit</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Lien pour les icônes -->
    <style>
        .product-image {
            height: 300px;
            object-fit: cover;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .card-price {
            font-size: 2rem;
            color: #28a745; /* Vert pour le prix */
            font-weight: bold;
            text-align: center;
        }
        .btn-lg-custom {
            font-size: 1.25rem; /* Taille du texte dans le bouton */
            padding: 0.75rem 1.25rem; /* Taille du padding */
        }
        .input-group-prepend .input-group-text {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
        }
        .input-group .form-control {
            border-radius: 0;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .custom-input-width {
            max-width: 150px; /* Ajuster la largeur comme souhaité */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="form-container">
            <h2 class="text-center mb-4">Acheter: <?php echo htmlspecialchars($produit['nom']); ?></h2>
            <div class="card mb-3">
                <img src="<?php echo htmlspecialchars($produit['image']); ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                <div class="card-body text-center">
                    <h3 class="card-title"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                    <p class="card-text"><strong>Catégorie:</strong> <?php echo htmlspecialchars($produit['categorie']); ?></p>
                    <p class="card-text"><?php echo htmlspecialchars($produit['description']); ?></p>
                    <p class="card-price">Prix: €<?php echo htmlspecialchars($produit['prix']); ?></p>
                    <!-- Formulaire de confirmation dans la carte -->
                    <form method="post">
                        <input type="hidden" name="idProduit" value="<?php echo htmlspecialchars($produit['idProduit']); ?>">
                        <div class="form-group">
                            <label for="quantite">Quantité souhaitée:</label>
                            <div class="input-group custom-input-width mx-auto">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-cube"></i></span>
                                </div>
                                <input type="number" class="form-control form-control-sm" id="quantite" name="quantite" min="1" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg btn-block btn-lg-custom">
                            <i class="fas fa-check-circle"></i> <!-- Icône de confirmation -->
                            Confirmer l'achat
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
