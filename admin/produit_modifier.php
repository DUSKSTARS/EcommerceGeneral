<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
exigerConnexion();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    header('Location: produits.php?msg=Produit introuvable');
    exit;
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY nom')->fetchAll();
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom          = trim($_POST['nom'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $prix         = (int)($_POST['prix'] ?? 0);
    $categorie_id = (int)($_POST['categorie_id'] ?? 0);

    $image = $produit['image'];
    $imag  = $produit['imag'];
    $ima   = $produit['ima'];
    $im    = $produit['im'];

    $dossierImages = __DIR__ . '/../images/';
    if (!is_dir($dossierImages)) mkdir($dossierImages, 0755, true);

    function uploadImage(string $champ, string $dossier, ?string $ancienne = null): ?string
    {
        if (empty($_FILES[$champ]['name'])) return $ancienne;

        if ($ancienne && $ancienne !== 'images/ima.jpg') {
            $ancienChemin = $dossier . '../' . $ancienne;
            if (file_exists($ancienChemin)) @unlink($ancienChemin);
        }

        $nomFichier = time() . '_' . uniqid() . '_' . basename($_FILES[$champ]['name']);
        $chemin     = $dossier . $nomFichier;

        if (move_uploaded_file($_FILES[$champ]['tmp_name'], $chemin)) {
            return 'images/' . $nomFichier;
        }
        return $ancienne;
    }

    $image = uploadImage('image', $dossierImages, $image);
    $imag  = uploadImage('imag',  $dossierImages, $imag);
    $ima   = uploadImage('ima',   $dossierImages, $ima);
    $im    = uploadImage('im',    $dossierImages, $im);

    if ($nom && $prix > 0 && $categorie_id) {
        $stmt = $pdo->prepare('
            UPDATE produits
            SET nom = ?, description = ?, prix = ?,
                image = ?, imag = ?, ima = ?, im = ?,
                categorie_id = ?
            WHERE id = ?
        ');
        $stmt->execute([$nom, $description, $prix, $image, $imag, $ima, $im, $categorie_id, $id]);

        header('Location: produits.php?msg=Produit modifié avec succès');
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
    <title>Modifier un produit</title>
    <link rel="stylesheet" href="../bootstrap-5.2.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-iconss/font/bootstrap-icons.css">
</head>
<body class="bg-light">

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
                        <i class="bi bi-pencil"></i> Modifier le produit #<?= $id ?>
                    </h3>

                    <?php if ($erreur): ?>
                        <div class="alert alert-danger small"><?= htmlspecialchars($erreur) ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control"
                                value="<?= htmlspecialchars($produit['nom']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($produit['description']) ?></textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Prix (FCFA)</label>
                                <input type="number" name="prix" class="form-control"
                                    value="<?= $produit['prix'] ?>" min="0" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Catégorie</label>
                                <select name="categorie_id" class="form-select" required>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?= $c['id'] ?>"
                                            <?= $c['id'] == $produit['categorie_id'] ? 'selected' : '' ?>>
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
                                <div class="mb-2">
                                    <img src="../<?= htmlspecialchars($produit['image']) ?>"
                                        style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                </div>
                                <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted d-block">Laisser vide pour conserver.</small>
                            </div>

                            <!-- IMAGE SECONDAIRE 1 -->
                            <div class="col-6 col-md-6">
                                <label class="form-label small">
                                    Image secondaire 1
                                </label>
                                <div class="mb-2">
                                    <?php if (!empty($produit['imag'])): ?>
                                        <img src="../<?= htmlspecialchars($produit['imag']) ?>"
                                            style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                    <?php else: ?>
                                        <div style="width:100px;height:100px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;border:1px dashed #ccc;border-radius:8px;color:#aaa;">
                                            <i class="bi bi-image fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="imag" class="form-control form-control-sm" accept="image/*">
                            </div>

                            <!-- IMAGE SECONDAIRE 2 -->
                            <div class="col-6 col-md-6">
                                <label class="form-label small">
                                    Image secondaire 2
                                </label>
                                <div class="mb-2">
                                    <?php if (!empty($produit['ima'])): ?>
                                        <img src="../<?= htmlspecialchars($produit['ima']) ?>"
                                            style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                    <?php else: ?>
                                        <div style="width:100px;height:100px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;border:1px dashed #ccc;border-radius:8px;color:#aaa;">
                                            <i class="bi bi-image fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="ima" class="form-control form-control-sm" accept="image/*">
                            </div>

                            <!-- IMAGE SECONDAIRE 3 -->
                            <div class="col-6 col-md-6">
                                <label class="form-label small">
                                    Image secondaire 3
                                </label>
                                <div class="mb-2">
                                    <?php if (!empty($produit['im'])): ?>
                                        <img src="../<?= htmlspecialchars($produit['im']) ?>"
                                            style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                    <?php else: ?>
                                        <div style="width:100px;height:100px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;border:1px dashed #ccc;border-radius:8px;color:#aaa;">
                                            <i class="bi bi-image fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="im" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>

                        <button class="btn btn-dark w-100 mt-4">
                            <i class="bi bi-check-circle"></i> Enregistrer les modifications
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>