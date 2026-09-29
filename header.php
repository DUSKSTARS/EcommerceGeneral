<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma boutique</title>
    <link rel="stylesheet" href="bootstrap-5.2.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap-iconss/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navigation navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Ma Boutique</a>

        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="#" id="menuAccueil">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#produits" id="menuProduits">Produits</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link position-relative" href="#" id="boutonPanier">
                        <i class="bi bi-cart3"></i> Panier
                        <span id="compteurPanier"
                            class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
            </ul>

            <form class="d-flex" id="formRecherche">
                <ul class="navbar-nav me-auto mb-2 me-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-dark" href="login.php">
                            <i class="bi bi-person-lock"></i> Connexion
                        </a>
                    </li>
                </ul>
                <input class="form-control me-2" type="search" id="champRecherche"
                    placeholder="Rechercher un produit..." autocomplete="off">
            </form>
        </div>
    </div>
</nav>
