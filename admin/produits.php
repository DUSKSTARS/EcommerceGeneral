<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
exigerConnexion();

$produits = $pdo->query('
    SELECT p.*, c.nom AS categorie_nom
    FROM produits p
    JOIN categories c ON c.id = p.categorie_id
    ORDER BY p.id DESC
')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Produits</title>
    <link rel="stylesheet" href="../bootstrap-5.2.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-iconss/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark py-2">
    <div class="container-fluid container-lg">
        <a class="navbar-brand fs-6 fs-sm-5 mb-0" href="index.php">
            <i class="bi bi-shop"></i> Admin
        </a>
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid container-lg py-4 px-3">

    <div class="d-flex flex-column flex-sm-row justify-content-between
                align-items-stretch align-items-sm-center gap-2 mb-4">
        <h2 class="fs-4 fs-sm-3 mb-0">Liste des produits</h2>
        <a href="produit_ajouter.php" class="btn btn-dark btn-sm">
            <i class="bi bi-plus-circle"></i> Ajouter un produit
        </a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success small"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <!-- VERSION DESKTOP : tableau -->
    <div class="card shadow-sm d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($produits)): ?>
                    <tr><td colspan="6" class="text-center py-4">Aucun produit</td></tr>
                <?php else: ?>
                    <?php foreach ($produits as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td>
                                <img src="../<?= htmlspecialchars($p['image']) ?>"
                                    style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                            </td>
                            <td><?= htmlspecialchars($p['nom']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($p['categorie_nom']) ?></span></td>
                            <td><?= number_format($p['prix'], 0, ',', ' ') ?> FCFA</td>
                            <td class="text-end">
                                <a href="produit_modifier.php?id=<?= $p['id'] ?>"
                                    class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="produit_supprimer.php?id=<?= $p['id'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Supprimer ce produit ?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- VERSION MOBILE : cartes -->
    <div class="d-md-none">
        <?php if (empty($produits)): ?>
            <div class="alert alert-info text-center">Aucun produit</div>
        <?php else: ?>
            <?php foreach ($produits as $p): ?>
                <div class="card shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex gap-3">
                            <img src="../<?= htmlspecialchars($p['image']) ?>"
                                style="width:70px;height:70px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="mb-1 text-truncate"><?= htmlspecialchars($p['nom']) ?></h6>
                                <span class="badge bg-secondary mb-1"><?= htmlspecialchars($p['categorie_nom']) ?></span>
                                <div class="fw-bold text-primary">
                                    <?= number_format($p['prix'], 0, ',', ' ') ?> FCFA
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <a href="produit_modifier.php?id=<?= $p['id'] ?>"
                                class="btn btn-sm btn-warning flex-fill">
                                <i class="bi bi-pencil"></i> Modifier
                            </a>
                            <a href="produit_supprimer.php?id=<?= $p['id'] ?>"
                                class="btn btn-sm btn-danger flex-fill"
                                onclick="return confirm('Supprimer ce produit ?')">
                                <i class="bi bi-trash"></i> Supprimer
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

</body>
</html>