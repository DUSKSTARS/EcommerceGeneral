<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
exigerConnexion();

$categories = $pdo->query('SELECT * FROM categories ORDER BY nom')->fetchAll();
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom          = trim($_POST['nom'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $prix         = (int)($_POST['prix'] ?? 0);
    $categorie_id = (int)($_POST['categorie_id'] ?? 0);

    // =========================================================
    // GESTION DES IMAGES (image obligatoire + imag/ima/im optionnelles)
    // =========================================================

    $dossierImages = __DIR__ . '/../images/';
    if (!is_dir($dossierImages)) {
        mkdir($dossierImages, 0755, true);
    }

    // Fonction utilitaire : upload une image et retourne son chemin
    function uploadImage(string $champ, string $dossier, ?string $valeurDefaut = null): ?string
    {
        if (empty($_FILES[$champ]['name'])) {
            return $valeurDefaut;
        }

        $nomFichier = time() . '_' . uniqid() . '_' . basename($_FILES[$champ]['name']);
        $chemin     = $dossier . $nomFichier;

        if (move_uploaded_file($_FILES[$champ]['tmp_name'], $chemin)) {
            return 'images/' . $nomFichier;
        }

        return $valeurDefaut;
    }

    // Image principale (fallback sur images/ima.jpg si non fournie)
    $image = uploadImage('image', $dossierImages, 'images/ima.jpg');

    // Images secondaires (optionnelles)
    $imag = uploadImage('imag', $dossierImages);
    $ima  = uploadImage('ima',  $dossierImages);
    $im   = uploadImage('im',   $dossierImages);

    // =========================================================
    // INSERTION EN BASE
    // =========================================================

    if ($nom && $prix > 0 && $categorie_id) {
        $stmt = $pdo->prepare('
            INSERT INTO produits
                (nom, description, prix, image, imag, ima, im, categorie_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([$nom, $description, $prix, $image, $imag, $ima, $im, $categorie_id]);

        header('Location: produits.php?msg=Produit ajouté avec succès');
        exit;
    } else {
        $erreur = 'Veuillez remplir correctement tous les champs.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
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
        <a href="produits.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</nav>

<div class="container-fluid container-lg py-4 px-3">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <h3 class="mb-4 fs-5 fs-md-4">
                        <i class="bi bi-plus-circle"></i> Nouveau produit
                    </h3>

                    <?php if ($erreur): ?>
                        <div class="alert alert-danger small"><?= htmlspecialchars($erreur) ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nom du produit</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Prix (FCFA)</label>
                                <input type="number" name="prix" class="form-control" min="0" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Catégorie</label>
                                <select name="categorie_id" class="form-select" required>
                                    <option value="">— Choisir —</option>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?= $c['id'] ?>">
                                            <?= htmlspecialchars($c['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-muted mb-3 small fw-bold">
                            <i class="bi bi-images"></i> Images du produit
                        </h6>

                        <div class="row g-3">
                            <!-- IMAGE PRINCIPALE -->
                            <div class="col-12 col-md-6">
                                <label class="form-label small">
                                    Image principale <span class="text-danger">*</span>
                                </label>
                                <input type="file" name="image" class="form-control form-control-sm"
                                    accept="image/*" required>
                                <small class="text-muted d-block">Affichée en priorité sur la boutique.</small>
                            </div>

                            <!-- IMAGE SECONDAIRE 1 -->
                            <div class="col-6 col-md-6">
                                <label class="form-label small">
                                    Image secondaire 1 <span class="text-muted">(optionnel)</span>
                                </label>
                                <input type="file" name="imag" class="form-control form-control-sm" accept="image/*">
                            </div>

                            <!-- IMAGE SECONDAIRE 2 -->
                            <div class="col-6 col-md-6">
                                <label class="form-label small">
                                    Image secondaire 2 <span class="text-muted">(optionnel)</span>
                                </label>
                                <input type="file" name="ima" class="form-control form-control-sm" accept="image/*">
                            </div>

                            <!-- IMAGE SECONDAIRE 3 -->
                            <div class="col-6 col-md-6">
                                <label class="form-label small">
                                    Image secondaire 3 <span class="text-muted">(optionnel)</span>
                                </label>
                                <input type="file" name="im" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>

                        <button class="btn btn-dark w-100 mt-4">
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