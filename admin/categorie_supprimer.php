<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
exigerConnexion();

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
}

header('Location: categories.php?msg=Catégorie supprimée avec succès');
exit;