<?php
// Inclure le fichier de connexion à la base de données
require_once 'bdd.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Vendeur</title>
    <!-- Inclure Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Inclure Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .card {
            color: white;
            text-align: center;
            border: none;
            margin: 10px;
            padding: 20px;
            flex: 1;
            max-width: 250px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 200px; /* Hauteur fixe pour uniformiser */
        }
        .card-icon {
            font-size: 3rem; /* Grande taille pour les icônes */
        }
        .card-text {
            font-size: 1.2rem; /* Taille du texte */
        }
        .card:hover {
            opacity: 0.8; /* Réduit l'opacité au survol */
            cursor: pointer;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .card-ajouter {
            background-color: #28a745; /* Vert pour ajouter */
        }
        .card-modifier {
            background-color: #ffc107; /* Jaune pour modifier */
        }
        .card-supprimer {
            background-color: #dc3545; /* Rouge pour supprimer */
        }
        .card-deconnexion {
            background-color: #007bff; /* Bleu pour déconnexion */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card-container">
            <!-- Les commandes passees -->
            <div class="card card-ajouter" onclick="location.href='liste_commandes.php';">
                <div class="card-body">
                    <i class="fas fa-shopping-cart card-icon"></i>
                    <div class="card-text">Les commandes passees</div>
                </div>
            </div>
            
            <!-- Ajouter un produit -->
            <div class="card card-ajouter bg-secondary" onclick="location.href='ajouter_produit.php';">
                <div class="card-body">
                    <i class="fas fa-plus card-icon"></i>
                    <div class="card-text">Ajouter un produit</div>
                </div>
            </div>

            <!-- Modifier un produit -->
            <div class="card card-modifier" onclick="location.href='gerer_produits.php';">
                <div class="card-body">
                    <i class="fas fa-edit card-icon"></i>
                    <div class="card-text">Gerer les produits</div>
                </div>
            </div>

            <!-- Déconnexion -->
            <div class="card card-deconnexion" onclick="location.href='deconnexion.php';">
                <div class="card-body">
                    <i class="fas fa-sign-out-alt card-icon"></i>
                    <div class="card-text">Déconnexion</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Inclure Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
