<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
exigerConnexion();

$categories = $pdo->query('
    SELECT c.*, COUNT(p.id) AS nb_produits
    FROM categories c
    LEFT JOIN produits p ON p.categorie_id = c.id
    GROUP BY c.id
    ORDER BY c.id
')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Catégories</title>
    <link rel="stylesheet" href="../bootstrap-5.2.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-iconss/font/bootstrap-icons.css">
</head>
<body class="bg-light">

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
        <h2 class="fs-4 fs-sm-3 mb-0">Liste des catégories</h2>
        <a href="categorie_ajouter.php" class="btn btn-dark btn-sm">
            <i class="bi bi-plus-circle"></i> Ajouter une catégorie
        </a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success small"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <!-- DESKTOP : tableau -->
    <div class="card shadow-sm d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>Nb produits</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="5" class="text-center py-4">Aucune catégorie</td></tr>
                <?php else: ?>
                    <?php foreach ($categories as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= htmlspecialchars($c['nom']) ?></td>
                            <td><code><?= htmlspecialchars($c['slug']) ?></code></td>
                            <td><span class="badge bg-info"><?= $c['nb_produits'] ?></span></td>
                            <td class="text-end">
                                <a href="categorie_modifier.php?id=<?= $c['id'] ?>"
                                    class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="categorie_supprimer.php?id=<?= $c['id'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Supprimer cette catégorie ? Les produits associés seront aussi supprimés.')">
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

    <!-- MOBILE : cartes -->
    <div class="d-md-none">
        <?php if (empty($categories)): ?>
            <div class="alert alert-info text-center">Aucune catégorie</div>
        <?php else: ?>
            <?php foreach ($categories as $c): ?>
                <div class="card shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0"><?= htmlspecialchars($c['nom']) ?></h6>
                            <span class="badge bg-info"><?= $c['nb_produits'] ?> produit(s)</span>
                        </div>
                        <code class="small text-muted d-block mb-3"><?= htmlspecialchars($c['slug']) ?></code>

                        <div class="d-flex gap-2">
                            <a href="categorie_modifier.php?id=<?= $c['id'] ?>"
                                class="btn btn-sm btn-warning flex-fill">
                                <i class="bi bi-pencil"></i> Modifier
                            </a>
                            <a href="categorie_supprimer.php?id=<?= $c['id'] ?>"
                                class="btn btn-sm btn-danger flex-fill"
                                onclick="return confirm('Supprimer cette catégorie ?')">
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