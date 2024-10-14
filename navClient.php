<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Client</title>
    <!-- Lien vers le CSS de Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lien vers Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-custom {
            background-color: #007bff; /* Couleur bleu vif pour la barre de navigation */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Ombre subtile pour le style */
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #ffffff; /* Couleur du texte blanche */
            font-size: 18px; /* Taille de police légèrement plus grande */
            font-weight: bold; /* Texte en gras pour les liens */
            transition: color 0.3s ease; /* Transition douce pour la couleur */
        }
        .navbar-custom .nav-link:hover {
            color: #ffd700; /* Couleur dorée pour le survol des liens */
        }
        .navbar-brand img {
            height: 100px; /* Augmente la hauteur de l'image */
            width: 100px; /* Augmente la largeur de l'image */
            border-radius: 50%; /* Rend l'image arrondie */
            margin-right: 10px; /* Espacement à droite du logo */
            object-fit: cover; /* Assure que l'image garde ses proportions tout en remplissant le cadre */
        }
        .nav-link i {
            margin-right: 8px; /* Espacement à droite de l'icône */
        }
    </style>
</head>
<body>
    <!-- Menu de navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="accueil.php">
            <!-- Logo ou image du nom de l'application -->
            <img src="images/tortue.jpg" alt="Logo"> Nom de l'Application
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="accueilClient.php">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="panier.php">
                        <i class="fas fa-shopping-cart"></i> Mon panier
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="deconnexion.php">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Contenu principal de la page -->
    <div class="container mt-4">
        <!-- Le contenu spécifique à chaque page viendra ici -->
    </div>

    <!-- Lien vers le JavaScript de Bootstrap et les dépendances -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
