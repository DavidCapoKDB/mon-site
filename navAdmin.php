<?php
// Inclure le fichier de connexion à la base de données
require_once 'bdd.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Admin</title>
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
        .card-les-clients {
            background-color: #ffc107; /* Jaune pour les clients */
        }
        .card-les-vendeurs {
            background-color: #dc3545; /* Rouge pour les vendeurs */
        }
        .card-deconnexion {
            background-color: #007bff; /* Bleu pour déconnexion */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card-container">
            <!-- Ajouter un client -->
            <div class="card card-ajouter" onclick="location.href='ajouter_client.php';">
                <div class="card-body">
                    <i class="fas fa-user-plus card-icon"></i>
                    <div class="card-text">Ajouter un client</div>
                </div>
            </div>

            <!-- Ajouter un vendeur -->
            <div class="card card-ajouter bg-secondary" onclick="location.href='ajouter_vendeur.php';">
                <div class="card-body">
                    <i class="fas fa-user-plus card-icon"></i>
                    <div class="card-text">Ajouter un vendeur</div>
                </div>
            </div>

            <!-- Les clients -->
            <div class="card card-les-clients" onclick="location.href='clients.php';">
                <div class="card-body">
                    <i class="fas fa-users card-icon"></i>
                    <div class="card-text">Les clients</div>
                </div>
            </div>

            <!-- Les vendeurs -->
            <div class="card card-les-vendeurs" onclick="location.href='vendeurs.php';">
                <div class="card-body">
                    <i class="fas fa-store card-icon"></i>
                    <div class="card-text">Les vendeurs</div>
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
