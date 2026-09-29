<?php
require_once 'config/database.php';

$email = 'admin@boutique.com';
$mdp   = 'admin123';
$hash  = password_hash($mdp, PASSWORD_DEFAULT);

// Supprime l'ancien admin s'il existe
$pdo->prepare('DELETE FROM utilisateurs WHERE email = ?')->execute([$email]);

// Insère le nouveau avec le bon hash
$pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?,?,?,?)')
    ->execute(['Administrateur', $email, $hash, 'admin']);

echo "✅ Admin créé avec succès !<br><br>";
echo "📧 Email : <b>$email</b><br>";
echo "🔑 Mot de passe : <b>$mdp</b><br><br>";
echo "<a href='login.php'>➡️ Aller à la page de connexion</a><br><br>";
echo "<small style='color:red;'>⚠️ Supprime ce fichier après utilisation pour la sécurité !</small>";