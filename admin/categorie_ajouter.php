<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
exigerConnexion();

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom  = trim($_POST['nom'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if ($slug === '') {
        $slug = strtolower($nom);
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
    }

    if ($nom && $slug) {
        try {
            $stmt = $pdo->prepare('INSERT INTO categories (nom, slug) VALUES (?, ?)');
            $stmt->execute([$nom, $slug]);
            header('Location: categories.php?msg=Catégorie ajoutée avec succès');
            exit;
        } catch (PDOException $e) {
            $erreur = 'Ce slug existe déjà.';
        }
    } else {
        $erreur = 'Veuillez remplir tous les champs.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une catégorie</title>
    <link rel="stylesheet" href="../bootstrap-5.2.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-iconss/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark py-2">
    <div class="container-fluid container-lg">
        <a class="navbar-brand fs-6 fs-sm-5 mb-0" href="index.php">
            <i class="bi bi-shop"></i> Admin
        </a>
        <a href="categories.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</nav>

<div class="container-fluid container-lg py-4 px-3">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <h3 class="mb-4 fs-5 fs-md-4">
                        <i class="bi bi-plus-circle"></i> Nouvelle catégorie
                    </h3>

                    <?php if ($erreur): ?>
                        <div class="alert alert-danger small"><?= htmlspecialchars($erreur) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug (laisser vide pour auto)</label>
                            <input type="text" name="slug" class="form-control"
                                placeholder="ex: electromenager">
                        </div>

                        <button class="btn btn-dark w-100">
                            <i class="bi bi-check-circle"></i> Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>