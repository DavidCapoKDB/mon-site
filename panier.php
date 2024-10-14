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

// Obtenez l'ID du client
$idClient = $_SESSION['user_id'];

// Si l'utilisateur confirme l'achat
if (isset($_POST['confirm_purchase'])) {
    // Préparer la requête pour mettre à jour le statut de la commande à "Commandé"
    $updateSql = "
        UPDATE commande c
        JOIN detailsCommande dc ON c.idCommande = dc.idCommande
        SET c.statut = 'Commandé'
        WHERE dc.idClient = ? AND c.statut = 'En attente de paiement'";
    
    $updateStmt = $conn->prepare($updateSql);

    if ($updateStmt === false) {
        die("Erreur lors de la préparation de la requête de mise à jour : " . $conn->error);
    }

    $updateStmt->bind_param("i", $idClient);

    if ($updateStmt->execute()) {
        // Définir une variable de session pour le message de succès
        $_SESSION['success_message'] = "Votre achat a été confirmé avec succès!";
        // Recharger la page pour afficher le message
        header("Location: panier.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la confirmation de l'achat.</div>";
    }
}

// Préparer la requête SQL pour obtenir les articles du panier
$sql = "SELECT d.idDetailCommande, d.idProduit, p.nom, p.description, p.prix, p.image, d.quantite, (p.prix * d.quantite) AS total
        FROM detailsCommande d
        JOIN commande c ON d.idCommande = c.idCommande
        JOIN produits p ON d.idProduit = p.idProduit
        WHERE d.idClient = ? AND c.statut = 'En attente de paiement'";

// Préparer la requête
$stmt = $conn->prepare($sql);

// Vérifiez si la préparation de la requête a échoué
if ($stmt === false) {
    die("Erreur lors de la préparation de la requête : " . $conn->error);
}

// Lier les paramètres et exécuter la requête
$stmt->bind_param("i", $idClient);
$stmt->execute();
$result = $stmt->get_result();

// Vérifiez si la requête a réussi
if ($result === false) {
    die("Erreur lors de l'exécution de la requête : " . $stmt->error);
}

// Calculer le montant total du panier
$totalPanier = 0;
while ($row = $result->fetch_assoc()) {
    $totalPanier += $row['total'];
}

// Si une action de suppression est soumise
if (isset($_POST['confirm_remove'])) {
    $idDetailCommande = $_POST['idDetailCommande'];
    $deleteSql = "DELETE FROM detailsCommande WHERE idDetailCommande = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    if ($deleteStmt === false) {
        die("Erreur lors de la préparation de la requête de suppression : " . $conn->error);
    }
    $deleteStmt->bind_param("i", $idDetailCommande);
    if ($deleteStmt->execute()) {
        // Recharger la page pour refléter les changements
        header("Location: panier.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la suppression du produit.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .product-card {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .product-card .card-img-top {
            object-fit: cover;
            width: 100%;
            height: 200px;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .btn-remove, .btn-purchase {
            margin-top: 10px;
            font-size: 1rem;
        }

        .total-container {
            margin-top: 20px;
            text-align: center;
        }

        .total-container .total-price {
            color: green;
            font-weight: bold;
        }

        .card-text.total {
            color: red;
            font-weight: bold;
        }

        .btn-center {
            display: flex;
            justify-content: center;
        }

        .modal-body p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- <h1 class="mb-4">Mon Panier</h1> -->

        <?php if (isset($_SESSION['success_message'])): ?>
            <div id="successMessage" class="alert alert-success">
                <?php
                echo htmlspecialchars($_SESSION['success_message']);
                // Supprimer le message de la session après l'affichage
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php
                // Réinitialiser le pointeur du résultat pour la boucle
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card product-card">
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nom']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['nom']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <p class="card-text">Prix Unitaire: €<?php echo htmlspecialchars($row['prix']); ?></p>
                                <p class="card-text">Quantité: <?php echo htmlspecialchars($row['quantite']); ?></p>
                                <p class="card-text total">Total: €<?php echo htmlspecialchars($row['total']); ?></p>
                                <button type="button" class="btn btn-danger btn-remove" data-toggle="modal" data-target="#confirmDeleteModal" data-id="<?php echo htmlspecialchars($row['idDetailCommande']); ?>" data-nom="<?php echo htmlspecialchars($row['nom']); ?>">
                                    <i class="fas fa-trash-alt"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="total-container">
                <h3 class="total-price">Total du panier : €<?php echo htmlspecialchars($totalPanier); ?></h3>
                <!-- Bouton Acheter -->
                <button type="button" class="btn btn-success mt-3" data-toggle="modal" data-target="#confirmPurchaseModal">
                    <i class="fas fa-shopping-cart"></i> Acheter
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                Votre panier est vide.
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer <strong id="productName"></strong> de votre panier ?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="post">
                        <input type="hidden" name="idDetailCommande" id="productId">
                        <button type="submit" name="confirm_remove" class="btn btn-danger">Supprimer</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation d'achat -->
    <div class="modal fade" id="confirmPurchaseModal" tabindex="-1" role="dialog" aria-labelledby="confirmPurchaseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmPurchaseModalLabel">Confirmer l'achat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir confirmer cet achat ?</p>
                </div>
                <div class="modal-footer">
                    <form id="purchaseForm" method="post">
                        <button type="submit" name="confirm_purchase" class="btn btn-success">Confirmer</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            // Afficher le message de succès et rediriger après 2 secondes
            var successMessage = $("#successMessage").text().trim();
            if (successMessage) {
                setTimeout(function() {
                    window.location.href = "panier.php";
                }, 2000); // 2000 ms = 2 secondes
            }

            // Initialiser le modal de suppression avec les données du produit
            $('#confirmDeleteModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Bouton qui a déclenché le modal
                var productName = button.data('nom'); // Nom du produit
                var productId = button.data('id'); // ID du produit

                var modal = $(this);
                modal.find('#productName').text(productName);
                modal.find('#productId').val(productId);
            });
        });
    </script>
</body>
</html>
