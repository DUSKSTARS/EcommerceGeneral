<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erreur' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['panier'])) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Panier vide']);
    exit;
}

$total = 0;
$details = [];

foreach ($data['panier'] as $item) {
    $sousTotal = $item['prix'] * $item['quantite'];
    $total += $sousTotal;
    $details[] = $item['nom'] . ' x' . $item['quantite'] . ' = ' . $sousTotal . ' FCFA';
}

try {
    $stmt = $pdo->prepare('
        INSERT INTO commandes (client_nom, client_contact, total, details)
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([
        $data['client']['nom'] ?? 'Anonyme',
        $data['client']['contact'] ?? '',
        $total,
        implode("\n", $details)
    ]);

    echo json_encode([
        'succes' => true,
        'commande_id' => $pdo->lastInsertId(),
        'total' => $total
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erreur' => $e->getMessage()]);
}