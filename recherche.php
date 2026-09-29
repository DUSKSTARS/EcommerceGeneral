<?php
require_once 'config/database.php';

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');

if ($q === '') {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare('
    SELECT p.*, c.nom AS categorie_nom
    FROM produits p
    JOIN categories c ON c.id = p.categorie_id
    WHERE p.nom LIKE ?
    ORDER BY p.nom
');
$stmt->execute(['%' . $q . '%']);
$resultats = $stmt->fetchAll();

echo json_encode($resultats);