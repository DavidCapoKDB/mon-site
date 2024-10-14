<?php
// Inclure le fichier de connexion à la base de données
include 'include/bdd.php';

$successMessage = "";
$errorMessage = "";

// Vérification de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des valeurs des champs
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom = htmlspecialchars($_POST['nom']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $confirmPassword = htmlspecialchars($_POST['confirm_password']);

    // Validation des données
    if ($password !== $confirmPassword) {
        $errorMessage = "Les mots de passe ne correspondent pas.";
    } else {
        // Préparation des requêtes d'insertion
        $conn->autocommit(FALSE); // Début de la transaction

        try {
            // Insérer dans la table utilisateurs
            $hashedPassword = hash('sha256', $password); // Hachage du mot de passe
            $sqlUser = "INSERT INTO utilisateurs (login, password, fonction) VALUES (?, ?, ?)";
            $stmtUser = $conn->prepare($sqlUser);
            $fonction = 'client';  // Définir la fonction comme client
            $stmtUser->bind_param("sss", $email, $hashedPassword, $fonction);
            $stmtUser->execute();
            $idUser = $conn->insert_id; // Obtenir l'ID du nouvel utilisateur
            
            // Insérer dans la table clients
            $sqlClient = "INSERT INTO clients (idClient, prenom, nom, adresse, telephone, email) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtClient = $conn->prepare($sqlClient);
            $stmtClient->bind_param("isssss", $idUser, $prenom, $nom, $adresse, $telephone, $email);
            $stmtClient->execute();

            $conn->commit(); // Valider la transaction

            $successMessage = "Compte client créé avec succès !";
        } catch (Exception $e) {
            $conn->rollback(); // Annuler la transaction en cas d'erreur
            $errorMessage = "Erreur lors de la création du compte : " . $e->getMessage();
        }

        // Fermeture des requêtes préparées
        $stmtUser->close();
        $stmtClient->close();
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte client</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }
        .header-container {
            background-color: #ffffff;
            padding: 20px;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header-container h1 {
            margin: 0;
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }
        .header-images {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .header-images img {
            height: 120px;
            width: 100%;
            border-radius: 15px;
            object-fit: cover;
            border: 4px solid #f1f1f1;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .header-images img:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        .login-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
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
        .alert {
            display: none;
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 500px;
            z-index: 1000;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
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

    <div class="login-container">
        <h3>Créer un compte client</h3>
        <form action="" method="post">
            <!-- Messages affichés juste après la balise ouvrante <form> -->
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
            
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                <input type="text" id="prenom" name="prenom" class="form-control" placeholder="Prénom" required>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                <input type="text" id="nom" name="nom" class="form-control" placeholder="Nom" required>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-home"></i></span>
                </div>
                <input type="text" id="adresse" name="adresse" class="form-control" placeholder="Adresse" required>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                </div>
                <input type="text" id="telephone" name="telephone" class="form-control" placeholder="Téléphone" required>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                </div>
                <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <input type="password" id="password" name="password" class="form-control" placeholder="Mot de passe" required>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirmer le mot de passe" required>
            </div>
            <button type="submit" class="btn">Créer un compte</button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php" class="btn-link">Déjà un compte ? Connectez-vous ici</a>
        </div>

    </div>
    <script>
        // Afficher les messages de succès ou d'erreur avec délai
        document.addEventListener("DOMContentLoaded", function() {
            var successMessage = "<?php echo $successMessage; ?>";
            var errorMessage = "<?php echo $errorMessage; ?>";

            if (successMessage) {
                var alertSuccess = document.querySelector(".alert-success");
                alertSuccess.textContent = successMessage;
                alertSuccess.style.display = "block";
                setTimeout(function() {
                    alertSuccess.style.display = "none";
                    window.location.href = "login.php";
                }, 2000); // Délai de 2 secondes
            }

            if (errorMessage) {
                var alertError = document.querySelector(".alert-danger");
                alertError.textContent = errorMessage;
                alertError.style.display = "block";
                setTimeout(function() {
                    alertError.style.display = "none";
                }, 2000); // Délai de 2 secondes
            }
        });
    </script>
</body>
</html>
