<?php

session_start();

// Vérifiez si l'utilisateur est connecté et a le rôle de vendeur
if (!isset($_SESSION['user_id']) || $_SESSION['user_function'] !== 'vendeur') {
    header('Location: login.php');
    exit();
}

require_once 'vendordompdf/dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Connexion à la base de données
include('include/bdd.php');

// Récupérer l'idCommande depuis l'URL
$idCommande = isset($_GET['idCommande']) ? intval($_GET['idCommande']) : 0;

// Récupérer les informations du vendeur connecté
$vendeurId = $_SESSION['user_id'];

// Préparer la requête SQL pour obtenir les détails de la commande
$sql = "
SELECT 
    c.prenom AS client_prenom, 
    c.nom AS client_nom, 
    c.adresse AS client_adresse, 
    c.telephone AS client_telephone, 
    c.email AS client_email,
    co.idCommande,
    DATE_FORMAT(co.dateCommande, '%d/%m/%Y') AS dateCommande,
    p.nom AS produit_nom, 
    p.categorie, 
    p.description, 
    p.prix, 
    dc.quantite
FROM clients c
JOIN detailsCommande dc ON c.idClient = dc.idClient
JOIN produits p ON dc.idProduit = p.idProduit
JOIN commande co ON dc.idCommande = co.idCommande
WHERE co.idCommande = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $idCommande);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Erreur de requête SQL : " . $conn->error);
}

// Préparer la requête SQL pour obtenir les détails du vendeur
$sqlVendeur = "
SELECT 
    v.prenom AS vendeur_prenom,
    v.nom AS vendeur_nom,
    v.telephone AS vendeur_telephone
FROM vendeurs v
JOIN utilisateurs u ON v.idVendeur = u.idUser
WHERE u.idUser = ?
";

$stmtVendeur = $conn->prepare($sqlVendeur);
$stmtVendeur->bind_param('i', $vendeurId);
$stmtVendeur->execute();
$resultVendeur = $stmtVendeur->get_result();

if (!$resultVendeur) {
    die("Erreur de requête SQL pour le vendeur : " . $conn->error);
}

// Instanciation et configuration de Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

// Chemin absolu de l'image du cachet
$cachetImagePath = 'C:/xampp/htdocs/projet_memoire/images/cachet.jpg'; // Modifiez ce chemin si nécessaire

// Création du contenu HTML pour le PDF avec du CSS
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        p {
            font-size: 14px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
        .cachet {
            text-align: center;
            margin-top: 40px;
        }
        .cachet img {
            max-width: 200px;
            height: auto;
        }
        .vendor-info, .client-info {
            margin-top: 20px;
        }
        .vendor-info {
            border-top: 2px solid #333;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .vendor-info .info {
            flex: 1;
            margin-right: 20px; /* Espace entre le nom/prénom et le téléphone */
        }
        .vendor-info .info:last-child {
            margin-right: 0; /* Pas de marge après le dernier élément */
        }
        .vendor-info h2, .client-info h2 {
            font-size: 16px;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Reçu de Commande</h1>';

// Ajouter les informations du client
if ($row = $result->fetch_assoc()) {
    $html .= '<div class="client-info">
        <h2>Informations du Client</h2>
        <p><strong>Client:</strong> ' . htmlspecialchars($row['client_prenom'] . ' ' . $row['client_nom']) . '</p>
        <p><strong>Adresse:</strong> ' . htmlspecialchars($row['client_adresse']) . '</p>
        <p><strong>Téléphone:</strong> ' . htmlspecialchars($row['client_telephone']) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($row['client_email']) . '</p>
        <p><strong>Date de Commande:</strong> ' . htmlspecialchars($row['dateCommande']) . '</p>';

    $html .= '<table>
    <thead>
        <tr>
            <th>Nom du Produit</th>
            <th>Catégorie</th>
            <th>Description</th>
            <th>Prix Unitaire</th>
            <th>Quantité</th>
        </tr>
    </thead>
    <tbody>';

    do {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['produit_nom']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['categorie']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['description']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['prix']) . ' €</td>';
        $html .= '<td>' . htmlspecialchars($row['quantite']) . '</td>';
        $html .= '</tr>';
    } while ($row = $result->fetch_assoc());

    $html .= '</tbody></table>';
} else {
    $html .= '<p>Aucune commande trouvée.</p>';
}

// Ajouter les informations du vendeur
if ($rowVendeur = $resultVendeur->fetch_assoc()) {
    $html .= '<div class="vendor-info">
        <div class="info">
            <h2>Informations du Vendeur</h2>
            <p><strong>Vendeur:</strong> ' . htmlspecialchars($rowVendeur['vendeur_prenom'] . ' ' . $rowVendeur['vendeur_nom']) . '</p>
        </div>
        <div class="info">
            <p><strong>Téléphone:</strong> ' . htmlspecialchars($rowVendeur['vendeur_telephone']) . '</p>
        </div>
    </div>';
}

$html .= '<div class="footer">
    Merci pour votre achat !
</div>
<div class="cachet">
    <img src="' . htmlspecialchars($cachetImagePath) . '" alt="Cachet">
</div>
</body>
</html>';

// Charger le contenu HTML dans Dompdf
$dompdf->loadHtml($html);

// (Optionnel) Configurer la taille du papier et l'orientation
$dompdf->setPaper('A4', 'portrait');

// Rendre le HTML comme PDF
$dompdf->render();

// Envoyer le PDF au navigateur
$dompdf->stream("recu_commande_$idCommande.pdf", array("Attachment" => 0)); // 0 pour ouvrir dans le navigateur, 1 pour télécharger

// Fermer la connexion
$conn->close();
?>
