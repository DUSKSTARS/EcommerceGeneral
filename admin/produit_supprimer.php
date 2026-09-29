<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
exigerConnexion();

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Récupérer l'image pour la supprimer du disque
    $stmt = $pdo->prepare('SELECT image FROM produits WHERE id = ?');
    $stmt->execute([$id]);
    $produit = $stmt->fetch();

    if ($produit && $produit['image'] !== 'images/ima.jpg') {
        $chemin = __DIR__ . '/../' . $produit['image'];
        if (file_exists($chemin)) unlink($chemin);
    }

    $pdo->prepare('DELETE FROM produits WHERE id = ?')->execute([$id]);
}

header('Location: produits.php?msg=Produit supprimé avec succès');
exit;