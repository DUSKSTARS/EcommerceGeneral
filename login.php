<?php
require_once 'config/database.php';
require_once 'config/mailer.php';
require_once 'includes/auth.php';

$erreur = '';
$message = '';
$afficherDeblocage = false;


// TRAITEMENT DU DÉBLOCAGE (quand l'admin saisit le code reçu)

if (isset($_POST['action']) && $_POST['action'] === 'debloquer') {
    $email = trim($_POST['email'] ?? '');
    $code  = strtoupper(trim($_POST['code'] ?? ''));

    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['code_deblocage']) {
        $codeValide   = password_verify($code, $user['code_deblocage']);
        $codeNonExpire = strtotime($user['code_expire']) > time();

        if ($codeValide && $codeNonExpire) {
            // ✅ Débloquer le compte
            $pdo->prepare('
                UPDATE utilisateurs
                SET tentatives = 0,
                    bloque_jusqua = NULL,
                    code_deblocage = NULL,
                    code_expire = NULL
                WHERE id = ?
            ')->execute([$user['id']]);

            $message = '✅ Compte débloqué ! Vous pouvez maintenant vous connecter.';
            $afficherDeblocage = false;
        } elseif (!$codeNonExpire) {
            $erreur = '⏰ Ce code a expiré. Veuillez attendre la fin du blocage.';
            $afficherDeblocage = true;
        } else {
            $erreur = '❌ Code incorrect.';
            $afficherDeblocage = true;
        }
    } else {
        $erreur = '❌ Aucune demande de déblocage en cours.';
        $afficherDeblocage = true;
    }
}


// TRAITEMENT DE LA CONNEXION

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] !== 'debloquer')) {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    if ($email && $mdp) {
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Vérifier si le compte est bloqué
            $estBloque = $user['bloque_jusqua'] && strtotime($user['bloque_jusqua']) > time();

            if ($estBloque) {
                $afficherDeblocage = true;
                $minutes = ceil((strtotime($user['bloque_jusqua']) - time()) / 60);
                $erreur = "🔒 Compte bloqué. Vérifiez votre email, ou patientez {$minutes} min.";
            }
            elseif (password_verify($mdp, $user['mot_de_passe'])) {
                // ✅ Connexion OK → reset des tentatives
                $pdo->prepare('
                    UPDATE utilisateurs
                    SET tentatives = 0, bloque_jusqua = NULL,
                        code_deblocage = NULL, code_expire = NULL
                    WHERE id = ?
                ')->execute([$user['id']]);

                $_SESSION['utilisateur_id']   = $user['id'];
                $_SESSION['utilisateur_nom']  = $user['nom'];
                $_SESSION['utilisateur_role'] = $user['role'];

                header('Location: admin/index.php');
                exit;
            }
            else {
                // ❌ Mot de passe incorrect → incrémenter
                $tentatives = (int)$user['tentatives'] + 1;

                if ($tentatives >= 3) {
                    // 🔒 Bloquer + générer le code + envoyer email
                    $code          = genererCodeDeblocage(9);
                    $codeHash      = password_hash($code, PASSWORD_DEFAULT);
                    $bloqueJusqua  = date('Y-m-d H:i:s', time() + 1800); // 30 min
                    $codeExpire    = date('Y-m-d H:i:s', time() + 1800);

                    $pdo->prepare('
                        UPDATE utilisateurs
                        SET tentatives = ?,
                            bloque_jusqua = ?,
                            code_deblocage = ?,
                            code_expire = ?
                        WHERE id = ?
                    ')->execute([$tentatives, $bloqueJusqua, $codeHash, $codeExpire, $user['id']]);

                    // =====================================================
                    // ENVOI DE L'EMAIL
                    // =====================================================
                    $emailSecours = getConfig($pdo, 'admin_email_secours', $user['email']);

                    $sujet = "🔒 Alerte : Compte bloqué - Ma Boutique";
                    $corps = "
                        <html>
                        <body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;'>
                            <div style='max-width:600px;margin:auto;background:#fff;padding:30px;border-radius:10px;'>
                                <h2 style='color:#dc3545;'>🔒 Alerte sécurité</h2>
                                <p>Bonjour,</p>
                                <p>Le compte <strong>{$user['email']}</strong> a été <strong>bloqué</strong>
                                après <strong>3 tentatives de connexion échouées</strong>.</p>

                                <div style='background:#f8f9fa;padding:20px;border-left:4px solid #0d6efd;margin:20px 0;'>
                                    <p style='margin:0;'>Pour débloquer le compte, utilisez ce code :</p>
                                    <h1 style='letter-spacing:5px;color:#0d6efd;font-size:36px;margin:15px 0;'>
                                        {$code}
                                    </h1>
                                    <p style='margin:0;font-size:12px;color:#666;'>
                                        Valable 30 minutes.
                                    </p>
                                </div>

                                <p>Saisissez ce code sur la page de connexion pour débloquer votre compte.</p>
                                <p style='color:#999;font-size:12px;margin-top:30px;'>
                                    Si vous n'êtes pas à l'origine de ces tentatives, changez immédiatement
                                    votre mot de passe après déblocage.
                                </p>
                            </div>
                        </body>
                        </html>
                    ";

                    envoyerEmail($emailSecours, $sujet, $corps);
                    error_log("=== TENTATIVE ENVOI EMAIL ===");
error_log("Destinataire : " . $emailSecours);
error_log("Sujet : " . $sujet);
error_log("Code : " . $code);
error_log("Résultat : " . (envoyerEmail($emailSecours, $sujet, $corps) ? "OK" : "ECHEC"));

                    $afficherDeblocage = true;
                    $message = "🔒 Compte bloqué. Un code de déblocage a été envoyé à l'administrateur.";
                } else {
                    $restant = 3 - $tentatives;
                    $pdo->prepare('UPDATE utilisateurs SET tentatives = ? WHERE id = ?')
                        ->execute([$tentatives, $user['id']]);
                    $erreur = "❌ Mot de passe incorrect. Il vous reste {$restant} tentative(s).";
                }
            }
        } else {
            // Email inexistant : on incrémente pas (pas de user), message générique
            $erreur = 'Email ou mot de passe incorrect.';
        }
    } else {
        $erreur = 'Veuillez remplir tous les champs.';
    }
}


// VÉRIFIER SI ON DOIT AFFICHER LA PAGE DE DÉBLOCAGE

if (isset($_GET['email'])) {
    $emailGet = trim($_GET['email']);
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $stmt->execute([$emailGet]);
    $userCheck = $stmt->fetch();

    if ($userCheck && $userCheck['bloque_jusqua'] && strtotime($userCheck['bloque_jusqua']) > time()) {
        $afficherDeblocage = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Ma Boutique</title>
    <link rel="stylesheet" href="bootstrap-5.2.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap-iconss/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-6 col-lg-5">

        <?php if ($afficherDeblocage): ?>
            <!-- ============================================= -->
            <!-- PAGE DE DÉBLOCAGE                             -->
            <!-- ============================================= -->
            <div class="card shadow border-danger">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock fs-1 text-danger"></i>
                        <h4 class="mt-2">Compte bloqué</h4>
                        <p class="text-muted small mb-0">
                            Saisissez le code à 9 caractères reçu par email pour débloquer.
                        </p>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-success small"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <?php if ($erreur): ?>
                        <div class="alert alert-danger small"><?= htmlspecialchars($erreur) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="action" value="debloquer">

                        <div class="mb-3">
                            <label class="form-label">Email du compte bloqué</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($_GET['email'] ?? $_POST['email'] ?? '') ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Code de déblocage</label>
                            <input type="text" name="code" class="form-control text-center"
                                style="letter-spacing:5px;font-size:20px;text-transform:uppercase;"
                                maxlength="9" placeholder="XXXXXXXXX" required>
                        </div>

                        <button class="btn btn-danger w-100">
                            <i class="bi bi-unlock"></i> Débloquer le compte
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="login.php" class="small text-muted">
                            <i class="bi bi-arrow-left"></i> Retour à la connexion
                        </a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- ============================================= -->
            <!-- PAGE DE CONNEXION NORMALE                     -->
            <!-- ============================================= -->
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">
                        <i class="bi bi-shield-lock"></i>
                        Connexion Admin
                    </h3>

                    <?php if ($erreur): ?>
                        <div class="alert alert-danger small"><?= htmlspecialchars($erreur) ?></div>
                    <?php endif; ?>

                    <?php if ($message): ?>
                        <div class="alert alert-success small"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="mot_de_passe" class="form-control" required>
                        </div>

                        <button class="btn btn-dark w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Se connecter
                        </button>
                    </form>

                    <p class="text-muted small mt-3 mb-0 text-center">
                        Email : admin@boutique.com — Mot de passe : admin123
                    </p>
                </div>
            </div>
        <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>