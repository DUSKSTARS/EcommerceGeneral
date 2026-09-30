<?php
require_once 'config/database.php';
require_once 'config/mailer.php';

// ⚠️ Active le debug AVANT l'appel
// (on doit le faire dans mailer.php car la fonction ne l'expose pas)

$ok = envoyerEmail(
    'duskaudace98@gmail.com',
    'Test PHPMailer - Ma Boutique',
    '<h1>Ça marche ! 🎉</h1><p>PHPMailer est bien configuré.</p>'
);

echo $ok ? "✅ Email envoyé !" : "❌ Échec de l'envoi.";