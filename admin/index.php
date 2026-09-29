<?php require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
exigerConnexion();
$totalProduits = $pdo->query('SELECT COUNT(*) FROM produits')->fetchColumn();
$totalCategories = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$totalCommandes = $pdo->query('SELECT COUNT(*) FROM commandes')->fetchColumn(); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
    <link rel="stylesheet" href="../bootstrap-5.2.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-iconss/font/bootstrap-icons.css">
</head>

<body class="bg-light"> <!-- ========================= NAVBAR ========================= -->
    <nav class="navbar navbar-dark bg-dark py-3">
        <div class="container-fluid"> <!-- LOGO / NOM --> <a class="navbar-brand fw-bold fs-5" href="index.php"> <i
                    class="bi bi-shop me-2"></i> Ma Boutique — Admin </a> <!-- INFORMATIONS ADMIN -->
            <div class="d-flex align-items-center gap-2 gap-md-3 text-white"> <span class="fs-6"> <i
                        class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['utilisateur_nom']) ?>
                </span> <a href="../logout.php" class="btn btn-outline-light btn-sm"> <i
                        class="bi bi-box-arrow-right me-1"></i> Déconnexion </a> </div>
        </div>
    </nav> <!-- ========================= CONTENU ========================= -->
    <div class="container-fluid container-lg py-4 py-md-5 px-3 px-md-4"> <!-- TITRE -->
        <div class="mb-4 mb-md-5">
            <h1 class="fw-bold fs-2 fs-md-1 mb-2"> Tableau de bord </h1>
            <p class="text-muted fs-6 fs-md-5 mb-0"> Gérez votre boutique depuis cet espace. </p>
        </div> <!-- ========================= CARTES ========================= -->
        <div class="row g-3 g-md-4"> <!-- ===================== PRODUITS ====================== -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4 p-md-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-muted mb-2"> Produits </h5>
                                <h2 class="fw-bold display-6 mb-0"> <?= $totalProduits ?> </h2>
                            </div> <i class="bi bi-box-seam display-5 text-primary"></i>
                        </div> <a href="produits.php" class="btn btn-dark btn-lg w-100 mt-4"> <i
                                class="bi bi-box-seam me-2"></i> Gérer les produits </a>
                    </div>
                </div>
            </div> <!-- ===================== CATÉGORIES ====================== -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4 p-md-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-muted mb-2"> Catégories </h5>
                                <h2 class="fw-bold display-6 mb-0"> <?= $totalCategories ?> </h2>
                            </div> <i class="bi bi-tags display-5 text-success"></i>
                        </div> <a href="categories.php" class="btn btn-dark btn-lg w-100 mt-4"> <i
                                class="bi bi-tags me-2"></i> Gérer les catégories </a>
                    </div>
                </div>
            </div> <!-- ===================== COMMANDES ====================== -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4 p-md-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-muted mb-2"> Commandes </h5>
                                <h2 class="fw-bold display-6 mb-0"> <?= $totalCommandes ?> </h2>
                            </div> <i class="bi bi-receipt display-5 text-warning"></i>
                        </div> <a href="commandes.php" class="btn btn-dark btn-lg w-100 mt-4"> <i
                                class="bi bi-receipt me-2"></i> Voir les commandes </a>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- ========================= BOOTSTRAP JS ========================= -->
    <script src="../bootstrap-5.2.3-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>