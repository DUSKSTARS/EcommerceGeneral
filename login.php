<?php require_once 'config/database.php';
require_once 'includes/auth.php';
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';
    if ($email && $mdp) {
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            $_SESSION['utilisateur_id'] = $user['id'];
            $_SESSION['utilisateur_nom'] = $user['nom'];
            $_SESSION['utilisateur_role'] = $user['role'];
            header('Location: admin/index.php');
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect.';
        }
    } else {
        $erreur = 'Veuillez remplir tous les champs.';
    }
} ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Ma Boutique</title>
    <link rel="stylesheet" href="bootstrap-5.2.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap-iconss/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-4">
        <div class="row justify-content-center w-100">
            <!-- Mobile : presque toute la largeur Tablette : largeur moyenne PC : largeur limitée -->
            <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4 p-sm-5">
                        <!-- TITRE -->
                        <div class="text-center mb-4">
                            <div class="mb-3"> <i class="bi bi-shield-lock-fill fs-1"></i> </div>
                            <h3 class="fw-bold mb-2"> Connexion Admin </h3>
                            <p class="text-muted mb-0"> Connectez-vous à votre espace d'administration </p>
                        </div> <!-- MESSAGE D'ERREUR --> <?php if ($erreur): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert"> <i
                                    class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div> <?= htmlspecialchars($erreur) ?> </div>
                            </div> <?php endif; ?>
                        <!-- FORMULAIRE -->
                        <form method="POST">
                            <!-- EMAIL -->
                            <div class="mb-4"> <label for="email" class="form-label fw-semibold"> <i
                                        class="bi bi-envelope me-1"></i> Adresse email </label> <input type="email"
                                    id="email" name="email" class="form-control form-control-lg"
                                    placeholder="exemple@email.com" autocomplete="email" required> </div>
                            <!-- MOT DE PASSE -->
                            <div class="mb-4"> <label for="mot_de_passe" class="form-label fw-semibold"> <i
                                        class="bi bi-lock me-1"></i> Mot de passe </label> <input type="password"
                                    id="mot_de_passe" name="mot_de_passe" class="form-control form-control-lg"
                                    placeholder="Votre mot de passe" autocomplete="current-password" required> </div>
                            <!-- BOUTON --> <button type="submit" class="btn btn-dark btn-lg w-100"> <i
                                    class="bi bi-box-arrow-in-right me-2"></i> Se connecter </button>
                        </form> <!-- INFORMATION -->
                        <div class="text-center mt-4">
                            <p class="text-muted small mb-0"> <i class="bi bi-info-circle me-1"></i> Mot de passe oublié
                                ? Contactez l'administrateur. </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="bootstrap-5.2.3-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>