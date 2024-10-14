<?php
session_start();

// Vérifiez si l'utilisateur est connecté et a le rôle d'admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_function'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Inclure le fichier de connexion à la base de données
require_once 'include/bdd.php';

// Gestion de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'supprimer') {
        $idVendeur = intval($_POST['id']);
        
        // Supprimer les références du vendeur dans la base de données
        $deleteQuery = "DELETE FROM vendeurs WHERE idVendeur = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param('i', $idVendeur);
        
        if ($stmt->execute()) {
            $message = "Vendeur supprimé avec succès.";
        } else {
            $message = "Erreur lors de la suppression : " . $stmt->error;
        }
        
        $stmt->close();
    } elseif ($_POST['action'] === 'modifier') {
        $idVendeur = intval($_POST['id']);
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $adresse = $_POST['adresse'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        
        // Modifier les informations du vendeur
        $updateQuery = "UPDATE vendeurs SET prenom = ?, nom = ?, adresse = ?, telephone = ?, email = ? WHERE idVendeur = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('sssssi', $prenom, $nom, $adresse, $telephone, $email, $idVendeur);
        
        if ($stmt->execute()) {
            $message = "Vendeur modifié avec succès.";
        } else {
            $message = "Erreur lors de la modification : " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Requête pour récupérer les vendeurs
$query = "SELECT idVendeur, prenom, nom, adresse, telephone, email FROM vendeurs";
$result = $conn->query($query);

if (!$result) {
    die("Erreur lors de l'exécution de la requête SQL : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Vendeurs</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }
        .container {
            margin-top: 20px;
        }
        table {
            background-color: #fff;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        .btn {
            margin-right: 5px;
        }
        .modal-header {
            background-color: #007bff;
            color: #fff;
        }
    </style>
    <script>
        function confirmAction(action, id) {
            const modalId = action === 'supprimer' ? '#supprimer-modal' : '#modifier-modal';
            const formId = action === 'supprimer' ? '#supprimer-form' : '#modifier-form';
            
            if (action === 'supprimer') {
                document.getElementById('delete-id').value = id;
                $(modalId).modal('show');
            } else if (action === 'modifier') {
                fetch('get_vendeur.php?id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('mod-id').value = data.idVendeur;
                        document.getElementById('mod-prenom').value = data.prenom;
                        document.getElementById('mod-nom').value = data.nom;
                        document.getElementById('mod-adresse').value = data.adresse;
                        document.getElementById('mod-telephone').value = data.telephone;
                        document.getElementById('mod-email').value = data.email;
                        $(modalId).modal('show');
                    });
            }
        }
    </script>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="accueilAdmin.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Retour
                </a>
                <div class="input-group w-50">
                    <input type="text" id="search" class="form-control" placeholder="Rechercher un produit...">
                </div>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($row['nom']); ?></td>
                        <td><?php echo htmlspecialchars($row['adresse']); ?></td>
                        <td><?php echo htmlspecialchars($row['telephone']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="confirmAction('modifier', <?php echo htmlspecialchars($row['idVendeur']); ?>)">Modifier</button>
                            <button class="btn btn-danger btn-sm" onclick="confirmAction('supprimer', <?php echo htmlspecialchars($row['idVendeur']); ?>)">Supprimer</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal pour la confirmation de suppression -->
    <div class="modal fade" id="supprimer-modal" tabindex="-1" role="dialog" aria-labelledby="supprimerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supprimerModalLabel">Confirmation de Suppression</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer ce vendeur ?
                </div>
                <div class="modal-footer">
                    <form id="supprimer-form" method="post">
                        <input type="hidden" name="id" id="delete-id">
                        <input type="hidden" name="action" value="supprimer">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour la modification -->
    <div class="modal fade" id="modifier-modal" tabindex="-1" role="dialog" aria-labelledby="modifierModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifierModalLabel">Modifier Vendeur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modifier-form" method="post">
                        <input type="hidden" name="id" id="mod-id">
                        <input type="hidden" name="action" value="modifier">
                        <div class="form-group">
                            <label for="mod-prenom">Prénom</label>
                            <input type="text" class="form-control" id="mod-prenom" name="prenom" required>
                        </div>
                        <div class="form-group">
                            <label for="mod-nom">Nom</label>
                            <input type="text" class="form-control" id="mod-nom" name="nom" required>
                        </div>
                        <div class="form-group">
                            <label for="mod-adresse">Adresse</label>
                            <input type="text" class="form-control" id="mod-adresse" name="adresse" required>
                        </div>
                        <div class="form-group">
                            <label for="mod-telephone">Téléphone</label>
                            <input type="text" class="form-control" id="mod-telephone" name="telephone" required>
                        </div>
                        <div class="form-group">
                            <label for="mod-email">Email</label>
                            <input type="email" class="form-control" id="mod-email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Modifier</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
