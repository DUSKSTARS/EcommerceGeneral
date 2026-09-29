<?php
// GESTION DE LA SESSION ADMIN

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estConnecte(): bool
{
    return isset($_SESSION['utilisateur_id']);
}

function exigerConnexion(): void
{
    if (!estConnecte()) {
        header('Location: login.php');
        exit;
    }
}