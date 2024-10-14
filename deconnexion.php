<?php
session_start();

// Détruire toutes les données de la session
$_SESSION = array();

// Si vous utilisez des cookies pour la session, les supprimer également
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruire la session
session_destroy();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Fonction pour rediriger après un délai
        function rediriger() {
            setTimeout(function() {
                window.location.href = 'login.php'; // Redirige vers la page de connexion
            }, 1000); // 1000 millisecondes = 1 seconde
        }
        
        // Appeler la fonction lors du chargement de la page
        window.onload = rediriger;
    </script>
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
    <div class="container">
        <div class="alert alert-success text-center mt-5">
            <strong>Vous avez été déconnecté avec succès !</strong><br>
        </div>
    </div>
</body>
</html>
