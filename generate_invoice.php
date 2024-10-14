<?php
require_once 'vendordompdf/dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Vérifier si l'utilisateur est connecté
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

$idUtilisateur = $_SESSION['user_id']; // ID de l'utilisateur connecté

$idCommande = $_GET['idCommande'];

include('include/bdd.php');

// Préparer la requête SQL pour obtenir les détails de la commande
$sql = "
SELECT 
    c.prenom, 
    c.nom, 
    c.adresse, 
    c.telephone, 
    c.email,
    co.idCommande,
    DATE_FORMAT(co.dateCommande, '%d/%m/%Y') AS dateCommande,
    p.nom AS produit_nom, 
    p.prix, 
    dc.quantite
FROM clients c
JOIN detailsCommande dc ON c.idClient = dc.idClient
JOIN produits p ON dc.idProduit = p.idProduit
JOIN commande co ON dc.idCommande = co.idCommande
WHERE co.idCommande = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idCommande);
$stmt->execute();
$result = $stmt->get_result();

// Préparer la requête SQL pour obtenir les informations du vendeur
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
$stmtVendeur->bind_param("i", $idUtilisateur);
$stmtVendeur->execute();
$resultVendeur = $stmtVendeur->get_result();

// Construction du contenu HTML pour le PDF
$html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 80%; margin: auto; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { text-align: right; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Facture</h1>';

// Ajouter les informations du vendeur


$total = 0;

while ($row = $result->fetch_assoc()) {
    $sousTotal = $row['prix'] * $row['quantite'];
    $total += $sousTotal;

    $html .= '
        <p><strong>Client:</strong> ' . htmlspecialchars($row['prenom']) . ' ' . htmlspecialchars($row['nom']) . '</p>
        <p><strong>Adresse:</strong> ' . htmlspecialchars($row['adresse']) . '</p>
        <p><strong>Téléphone:</strong> ' . htmlspecialchars($row['telephone']) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>
        <p><strong>Date de Commande:</strong> ' . htmlspecialchars($row['dateCommande']) . '</p>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>' . htmlspecialchars($row['produit_nom']) . '</td>
                    <td>' . htmlspecialchars(number_format($row['prix'], 2, ',', ' ')) . ' €</td>
                    <td>' . htmlspecialchars($row['quantite']) . '</td>
                    <td>' . htmlspecialchars(number_format($sousTotal, 2, ',', ' ')) . ' €</td>
                </tr>
            </tbody>
        </table>';
}
if ($rowVendeur = $resultVendeur->fetch_assoc()) {
    $html .= '
        <p><strong>Vendeur:</strong> ' . htmlspecialchars($rowVendeur['vendeur_prenom']) . ' ' . htmlspecialchars($rowVendeur['vendeur_nom']) . '</p>
        <p><strong>Téléphone du vendeur:</strong> ' . htmlspecialchars($rowVendeur['vendeur_telephone']) . '</p>';
}
$html .= '<p class="total"><strong>Total à payer:</strong> ' . htmlspecialchars(number_format($total, 2, ',', ' ')) . ' €</p>
    </div>
</body>
</html>';

$stmt->close();
$stmtVendeur->close();
$conn->close();

$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("facture_{$idCommande}.pdf", array("Attachment" => false));
?>
