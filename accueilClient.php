<?php
session_start();

// Vérifiez si l'utilisateur est connecté et a le rôle de client
if (!isset($_SESSION['user_id']) || $_SESSION['user_function'] !== 'client') {
    header('Location: login.php');
    exit();
}

// Inclure le fichier de connexion à la base de données
include 'include/bdd.php';
include 'navClient.php';

// Vérifiez la connexion à la base de données
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Traitement du formulaire de confirmation de l'achat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idProduit']) && isset($_POST['quantite'])) {
    $idProduit = $_POST['idProduit'];
    $quantite = $_POST['quantite'];
    $idClient = $_SESSION['user_id'];

    // Préparer la requête pour insérer une nouvelle commande
    $sqlCommande = "INSERT INTO commande (idProduit, statut) VALUES (?, 'En attente de paiement')";
    $stmtCommande = $conn->prepare($sqlCommande);
    $stmtCommande->bind_param("i", $idProduit);

    if ($stmtCommande->execute()) {
        $idCommande = $stmtCommande->insert_id; // Obtenir l'ID de la commande nouvellement insérée

        // Préparer la requête pour insérer les détails de la commande
        $sqlDetails = "INSERT INTO detailsCommande (idCommande, idClient, idProduit, quantite, prix) 
                       SELECT ?, ?, p.idProduit, ?, p.prix 
                       FROM produits p 
                       WHERE p.idProduit = ?";
        $stmtDetails = $conn->prepare($sqlDetails);
        $stmtDetails->bind_param("iiii", $idCommande, $idClient, $quantite, $idProduit);

        if ($stmtDetails->execute()) {
            $message = "Commande passée avec succès !";
        } else {
            $message = "Erreur lors de l'ajout des détails de la commande : " . $stmtDetails->error;
        }
    } else {
        $message = "Erreur lors de l'ajout de la commande : " . $stmtCommande->error;
    }

    // Fermer les statements
    if (isset($stmtCommande)) {
        $stmtCommande->close();
    }
    if (isset($stmtDetails)) {
        $stmtDetails->close();
    }
}

// Exécuter la requête SQL pour obtenir les produits
$sql = "SELECT idProduit, nom, description, prix, image FROM produits";
$result = $conn->query($sql);

// Vérifiez si la requête a réussi
if ($result === false) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Produits</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .card {
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
        .card img {
            height: 200px;
            object-fit: cover;
        }
        .card-body {
            text-align: center;
        }
        .card-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .card-text {
            margin-bottom: 15px;
        }
        .card-price {
            color: #28a745;
            font-size: 1.25rem;
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-add-to-cart {
            font-size: 1.5rem;
        }
        .btn-confirm {
            font-size: 1.25rem;
            padding: 0.75rem 1.25rem;
        }
        .quantity-form {
            display: none;
            text-align: center;
        }
        .input-group {
            width: 60%; /* Ajustez la largeur du champ de quantité */
            margin: 0 auto; /* Centrez le champ de quantité */
        }
        .input-group-prepend i {
            font-size: 1.25rem;
        }
        .input-group input {
            border-radius: 0.25rem;
            border: 1px solid #ced4da;
        }
    </style>
    <script>
        function showQuantityForm(button) {
            var cardBody = button.closest('.card-body');
            var quantityForm = cardBody.querySelector('.quantity-form');
            var addToCartButton = cardBody.querySelector('.btn-add-to-cart');
            
            addToCartButton.style.display = 'none';
            quantityForm.style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="container">
        <?php if (isset($message)): ?>
            <div class="alert alert-info text-center" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nom']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['nom']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="card-price">Prix: €<?php echo htmlspecialchars($row['prix']); ?></p>
                            <form class="quantity-form" method="post">
                                <input type="hidden" name="idProduit" value="<?php echo htmlspecialchars($row['idProduit']); ?>">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-cube"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="quantite" name="quantite" min="1" placeholder="Quantité" required>
                                </div>
                                <button type="submit" class="btn btn-success btn-confirm mt-2">
                                    <i class="fas fa-check-circle"></i> Confirmer l'achat
                                </button>
                            </form>
                            <button type="button" class="btn btn-primary btn-add-to-cart" onclick="showQuantityForm(this)">
                                <i class="fas fa-cart-plus"></i> Acheter
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
