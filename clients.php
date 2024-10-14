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
        $idClient = intval($_POST['id']);
        
        // Supprimer les références du client dans detailsCommande
        $deleteDetailsQuery = "DELETE FROM detailsCommande WHERE idClient = ?";
        $stmt = $conn->prepare($deleteDetailsQuery);
        $stmt->bind_param('i', $idClient);
        $stmt->execute();
        $stmt->close();
        
        // Supprimer le client
        $deleteClientQuery = "DELETE FROM clients WHERE idClient = ?";
        $stmt = $conn->prepare($deleteClientQuery);
        $stmt->bind_param('i', $idClient);
        
        $success = $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => $success]);
        exit();
    } elseif ($_POST['action'] === 'modifier') {
        $idClient = intval($_POST['id']);
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $adresse = $_POST['adresse'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        
        // Mettre à jour les informations du client
        $updateClientQuery = "UPDATE clients SET prenom = ?, nom = ?, adresse = ?, telephone = ?, email = ? WHERE idClient = ?";
        $stmt = $conn->prepare($updateClientQuery);
        $stmt->bind_param('sssssi', $prenom, $nom, $adresse, $telephone, $email, $idClient);
        
        $success = $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => $success]);
        exit();
    }
}

// Requête pour récupérer les clients
$query = "SELECT idClient, prenom, nom, adresse, telephone, email FROM clients";
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
    <title>Liste des Clients</title>
    <link rel="stylesheet" href="style.css">
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
            border-radius: 0.25rem;
            overflow: hidden;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        .btn {
            margin-right: 5px;
        }
        .modal-content {
            padding: 20px;
        }
        .modal-header, .modal-footer {
            border: none;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
    <script>
        function confirmDelete(id, prenom, nom, adresse, telephone, email) {
            document.getElementById('client-details').innerHTML = `
                <strong>Prénom :</strong> ${prenom}<br>
                <strong>Nom :</strong> ${nom}<br>
                <strong>Adresse :</strong> ${adresse}<br>
                <strong>Téléphone :</strong> ${telephone}<br>
                <strong>Email :</strong> ${email}
            `;
            document.getElementById('delete-form-id').value = id;
            $('#confirmDeleteModal').modal('show');
        }

        function showEditForm(id, prenom, nom, adresse, telephone, email) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-prenom').value = prenom;
            document.getElementById('edit-nom').value = nom;
            document.getElementById('edit-adresse').value = adresse;
            document.getElementById('edit-telephone').value = telephone;
            document.getElementById('edit-email').value = email;
            $('#editModal').modal('show');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Gérer la soumission des formulaires via AJAX
            document.getElementById('delete-form').addEventListener('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                
                fetch('clients.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#confirmDeleteModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                });
            });

            document.getElementById('edit-form').addEventListener('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                
                fetch('clients.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#editModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Erreur lors de la modification');
                    }
                });
            });
        });
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
        <table class="table table-bordered table-striped">
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
                            <button type="button" class="btn btn-warning btn-sm" onclick="showEditForm(
                                <?php echo htmlspecialchars($row['idClient']); ?>, 
                                '<?php echo htmlspecialchars($row['prenom']); ?>', 
                                '<?php echo htmlspecialchars($row['nom']); ?>', 
                                '<?php echo htmlspecialchars($row['adresse']); ?>', 
                                '<?php echo htmlspecialchars($row['telephone']); ?>', 
                                '<?php echo htmlspecialchars($row['email']); ?>'
                            )">Modifier</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(
                                <?php echo htmlspecialchars($row['idClient']); ?>, 
                                '<?php echo htmlspecialchars($row['prenom']); ?>', 
                                '<?php echo htmlspecialchars($row['nom']); ?>', 
                                '<?php echo htmlspecialchars($row['adresse']); ?>', 
                                '<?php echo htmlspecialchars($row['telephone']); ?>', 
                                '<?php echo htmlspecialchars($row['email']); ?>'
                            )">Supprimer</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteLabel">Confirmer la suppression</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="client-details"></div>
                    <form id="delete-form" action="clients.php" method="post">
                        <input type="hidden" id="delete-form-id" name="id" value="">
                        <input type="hidden" name="action" value="supprimer">
                        <p>Êtes-vous sûr de vouloir supprimer ce client ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de modification -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLabel">Modifier Client</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit-form" action="clients.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="form-group">
                            <label for="edit-prenom">Prénom</label>
                            <input type="text" class="form-control" id="edit-prenom" name="prenom" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-nom">Nom</label>
                            <input type="text" class="form-control" id="edit-nom" name="nom" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-adresse">Adresse</label>
                            <input type="text" class="form-control" id="edit-adresse" name="adresse" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-telephone">Téléphone</label>
                            <input type="text" class="form-control" id="edit-telephone" name="telephone" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-email">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email" required>
                        </div>
                        <input type="hidden" name="action" value="modifier">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
