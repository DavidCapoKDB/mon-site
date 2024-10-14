<?php
session_start();

// Vérifiez si l'utilisateur est connecté et a le rôle de client
if (!isset($_SESSION['user_id']) || $_SESSION['user_function'] !== 'client') {
    header('Location: login.php');
    exit();
}

// Inclure le fichier de connexion à la base de données
include 'include/bdd.php';
include 'include/navClient.php';
// Vérifiez la connexion à la base de données
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Préparer la requête SQL
$sql = "SELECT idProduit, nom, description, prix, image FROM produits";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erreur de préparation de la requête : " . $conn->error);
}

// Exécuter la requête
$stmt->execute();
$result = $stmt->get_result();
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
            font-size: 1.5rem; /* Augmenter la taille du nom du produit */
            margin-bottom: 15px;
        }
        .card-text {
            margin-bottom: 15px;
        }
        .card-price {
            color: #28a745; /* Couleur verte pour le prix */
            font-size: 1.25rem; /* Taille du texte pour le prix */
            text-align: center; /* Centrer le prix */
            margin-bottom: 15px;
        }
        .btn-add-to-cart {
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- <h2 class="text-center my-4">Liste des Produits</h2> -->
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nom']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['nom']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="card-price">Prix: €<?php echo htmlspecialchars($row['prix']); ?></p>
                            <form action="ajouter_panier.php" method="post">
                                <input type="hidden" name="idProduit" value="<?php echo htmlspecialchars($row['idProduit']); ?>">
                                <button type="submit" class="btn btn-primary btn-add-to-cart">
                                    <i class="fas fa-cart-plus"></i> Acheter
                                </button>
                            </form>
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
