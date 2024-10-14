<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Application E-Commerce</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
        }
        .header-container {
            background-color: #ffffff; /* Blanc pour un look épuré et lumineux */
            padding: 20px;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: space-between; /* Espacement entre le nom et les images */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Ombre plus marquée pour un effet plus prononcé */
        }
        .header-container h1 {
            margin: 0;
            font-size: 32px; /* Taille du texte pour le titre */
            font-weight: bold; /* Poids du texte fort pour un style audacieux */
            color: #007bff; /* Bleu vif pour le titre */
        }
        .header-images {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Disposition en grille avec 3 colonnes */
            gap: 10px; /* Espacement entre les images */
        }
        .header-images img {
            height: 120px; /* Taille agrandie des images */
            width: 100%; /* Largeur pleine pour les images */
            border-radius: 15px; /* Coins arrondis pour les images */
            object-fit: cover;
            border: 4px solid #f1f1f1; /* Bordure subtile autour des images */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Effets de transition doux */
        }
        .header-images img:hover {
            transform: scale(1.1); /* Zoom au survol */
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2); /* Ombre accrue au survol */
        }
    </style>
</head>
<body>
    <header class="header-container">
        <div class="application-name">
            <h1>Nom de l'Application</h1>
        </div>
        <div class="header-images">
            <img src="../images/tortue.jpg" alt="Produit 1">
            <img src="../images/th.jpg" alt="Produit 2">
            <img src="../images/OIP.jpg" alt="Produit 3">
        </div>
    </header>
    <!-- Ajoutez d'autres parties de votre page ici -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>
