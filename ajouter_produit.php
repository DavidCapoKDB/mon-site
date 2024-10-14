<?php
session_start();

// Inclure le fichier de connexion à la base de données
include 'include/bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_function'])) {
    header('Location: login.php'); // Rediriger vers la page de connexion
    exit();
}

// Variables pour les messages
$successMessage = '';
$error = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $categorie = trim($_POST['categorie']);
    $description = trim($_POST['description']);
    $prix = trim($_POST['prix']);
    $image = $_FILES['image'];

    // Vérifier que les champs ne sont pas vides
    if (empty($nom) || empty($prix) || empty($image['name'])) {
        $error = 'Veuillez remplir tous les champs et sélectionner une image.';
    } else {
        // Vérifier et traiter l'image
        $targetDir = "uploads/";
        $imageName = basename($image['name']);
        $targetFilePath = $targetDir . $imageName;

        if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
            // Préparer et exécuter la requête pour ajouter le produit
            $stmt = $conn->prepare('INSERT INTO produits (nom, categorie, description, prix, image) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $nom, $categorie, $description, $prix, $targetFilePath);

            if ($stmt->execute()) {
                $successMessage = 'Produit ajouté avec succès!';
            } else {
                $error = 'Erreur lors de l\'ajout du produit: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            $error = 'Erreur lors du téléchargement de l\'image.';
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Produit</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
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
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h3 {
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
    <script>
        function showTemporaryMessage(type, message) {
            var messageContainer = document.getElementById('message-container');
            messageContainer.className = 'alert alert-' + type;
            messageContainer.textContent = message;
            messageContainer.style.display = 'block';

            setTimeout(function() {
                messageContainer.style.display = 'none';
            }, 2000);
        }
    </script>
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
    <div class="container">
        <div class="form-container">
            <h3>Ajouter un Produit</h3>
            <div id="message-container" style="display:none;"></div>
            <?php if (!empty($successMessage)): ?>
                <script>
                    window.onload = function() {
                        showTemporaryMessage('success', '<?php echo addslashes($successMessage); ?>');
                    }
                </script>
            <?php elseif (!empty($error)): ?>
                <script>
                    window.onload = function() {
                        showTemporaryMessage('danger', '<?php echo addslashes($error); ?>');
                    }
                </script>
            <?php endif; ?>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-box"></i></span>
                    </div>
                    <input type="text" id="nom" name="nom" class="form-control" placeholder="Nom du produit" required>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-tags"></i></span>
                    </div>
                    <input type="text" id="categorie" name="categorie" class="form-control" placeholder="Catégorie">
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                    </div>
                    <textarea id="description" name="description" class="form-control" placeholder="Description" rows="3"></textarea>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                    </div>
                    <input type="number" id="prix" name="prix" class="form-control" placeholder="Prix" step="0.01" required>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-image"></i></span>
                    </div>
                    <input type="file" id="image" name="image" class="form-control-file" accept="image/*" required>
                </div>
                <button type="submit" class="btn">Ajouter le produit</button>
            </form>
            <div class="text-center mt-3">
            <a href="accueilVendeur.php">Retour a l'accueil</a>    
        </div>
        </div>
        
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
