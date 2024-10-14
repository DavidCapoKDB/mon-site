<?php
session_start();

// Inclure le fichier de connexion à la base de données
include 'include/bdd.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    // Vérifier que les champs ne sont pas vides
    if (empty($login) || empty($password)) {
        $error = 'Veuillez entrer votre nom d\'utilisateur et mot de passe.';
    } else {
        // Préparer et exécuter la requête pour récupérer les informations de l'utilisateur
        $stmt = $conn->prepare('SELECT idUser, password, fonction FROM utilisateurs WHERE login = ?');
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $stmt->store_result();
        
        // Vérifier si l'utilisateur existe
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($idUser, $hashedPassword, $fonction);
            $stmt->fetch();

            // Vérifier si le mot de passe est correct
            if (hash('sha256', $password) === $hashedPassword) {
                // Initialiser les variables de session
                $_SESSION['user_id'] = $idUser;
                $_SESSION['user_function'] = $fonction;

                // Rediriger en fonction de la fonction de l'utilisateur
                switch ($fonction) {
                    case 'admin':
                        header('Location: accueilAdmin.php');
                        exit();
                    case 'vendeur':
                        header('Location: accueilVendeur.php');
                        exit();
                    case 'client':
                        header('Location: accueilClient.php');
                        exit();
                    default:
                        $error = 'Rôle utilisateur inconnu.';
                }
            } else {
                $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
            }
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
        }

        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
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
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }
        .login-container {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .login-container h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .form-control {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .text-center {
            text-align: center;
        }
        .mt-3 {
            margin-top: 15px;
        }
    </style>
</head>
<header class="header-container">
        <div class="application-name">
            <h1>Nom de l'Application</h1>
        </div>
        <div class="header-images">
            <img src="images/tortue.jpg" alt="Produit 1">
            <img src="images/th.jpg" alt="Produit 2">
            <img src="images/OIP.jpg" alt="Produit 3">
        </div>
    </header>
<body>
    <div class="container">
        <div class="login-container">
            <h3>Connexion</h3>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="" method="post">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" id="login" name="login" class="form-control" placeholder="Nom d'utilisateur" required>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Mot de passe" required>
                </div>
                <button type="submit" class="btn">Se connecter</button>
            </form>
            <div class="text-center mt-3">
                <a href="creerUnCompte.php">Créer un compte client</a>
                <br>
                <a href="motDePasseOublie.php">Mot de passe oublié</a>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
