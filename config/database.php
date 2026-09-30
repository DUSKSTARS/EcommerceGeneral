<?php
// CONNEXION À LA BASE DE DONNÉES

$host   = 'localhost';
$dbname = 'ma_boutique';
$user   = 'root';
$pass   = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

function genererCodeDeblocage(int $longueur = 9): string
{
    $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < $longueur; $i++) {
        $code .= $caracteres[random_int(0, strlen($caracteres) - 1)];
    }
    return $code;
}

function getConfig(PDO $pdo, string $cle, string $defaut = ''): string
{
    try {
        $stmt = $pdo->prepare('SELECT valeur FROM config WHERE cle = ?');
        $stmt->execute([$cle]);
        return $stmt->fetchColumn() ?: $defaut;
    } catch (Exception $e) {
        return $defaut;
    }
}
