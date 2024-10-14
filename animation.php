<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animation de Fond</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            overflow: hidden;
        }

        .bg-animation {
            position: fixed;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            animation: slideShow 15s infinite;
            z-index: -1;
            background-repeat: no-repeat;
        }

        @keyframes slideShow {
            0% { background-image: url('../images/tortue.jpg'); }
            33% { background-image: url('../images/th.jpg'); }
            66% { background-image: url('../images/OIP.jpg'); }
            100% { background-image: url('../images/tortue.jpg'); }
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Overlay sombre pour meilleure lisibilité */
            z-index: 0;
        }

        .content {
            position: relative;
            z-index: 1; /* Assure que le contenu est au-dessus de la superposition */
            color: #fff;
            text-align: center;
            padding-top: 20%;
        }

        .content h1 {
            font-size: 4rem;
            margin: 0;
        }

        .content p {
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="bg-animation"></div>
    <div class="bg-overlay"></div>
    <div class="content">
        <!-- Contenu ici -->
    </div>
</body>
</html>
