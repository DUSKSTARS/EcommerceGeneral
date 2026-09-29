<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

// Récupération des catégories
$categories = $pdo->query('SELECT * FROM categories ORDER BY id')->fetchAll();

// Récupération des produits (avec catégorie)
$produits = $pdo->query('
    SELECT p.*, c.slug AS categorie_slug
    FROM produits p
    JOIN categories c ON c.id = p.categorie_id
    ORDER BY p.id
')->fetchAll();

// Catégorie par défaut affichée
$categorieActive = 'ustensiles';
?>

<!-- le html  -->

<?php include 'header.php'; ?>


<!-- HERO -->
<section class="hero">
    <div class="hero-wave"></div>
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-12 col-lg-6 hero-text">
                <span class="hero-label">OFFRE SPÉCIALE</span>
                <h1>Découvrez nos <span>meilleurs produits</span></h1>
                <p>Des produits de qualité au meilleur prix.
                   Découvrez notre collection et commandez facilement
                   depuis votre téléphone.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#produits" class="btn btn-dark btn-lg">Découvrir les produits</a>
                    <a href="#contact" class="btn btn-outline-dark btn-lg">Nous contacter</a>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="hero-image">
                    <img src="images/ima.jpg" alt="Produits de la boutique">
                </div>
            </div>
        </div>
    </div>
</section>

<div class="espace"></div>

<!-- CONTENU DE LA BOUTIQUE -->
<div id="contenuBoutique">

    <section class="categories-section py-5" id="produits">
        <div class="container">
            <h2 class="text-center mb-4">Nos catégories</h2>

            <!-- BOUTONS CATEGORIES (dynamiques) -->
            <div class="d-flex justify-content-center flex-wrap gap-2 mb-5">
                <?php foreach ($categories as $i => $cat): ?>
                    <button class="btn <?= $i === 0 ? 'btn-dark active' : 'btn-outline-dark' ?> filtre-btn"
                        data-categorie="<?= htmlspecialchars($cat['slug']) ?>">
                        <?= htmlspecialchars($cat['nom']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

<!-- PRODUITS (dynamiques) -->
<div class="row g-4" id="liste-produits">
    <?php foreach ($produits as $p): ?>
        <?php
            $images = array_filter([
                $p['image'],
                $p['imag'],
                $p['ima'],
                $p['im'],
            ]);
            $imagesJson = htmlspecialchars(json_encode(array_values($images)), ENT_QUOTES);
        ?>
        <div class="col-12 col-md-6 col-lg-4 produit"
            data-categorie="<?= htmlspecialchars($p['categorie_slug']) ?>">

            <div class="card h-100 shadow-sm produit-carte"
                data-id="<?= $p['id'] ?>"
                data-nom="<?= htmlspecialchars($p['nom']) ?>"
                data-description="<?= htmlspecialchars($p['description']) ?>"
                data-prix="<?= $p['prix'] ?>"
                data-image="<?= htmlspecialchars($p['image']) ?>"
                data-images='<?= $imagesJson ?>'>

                <img src="<?= htmlspecialchars($p['image']) ?>"
                    class="card-img-top" alt="<?= htmlspecialchars($p['nom']) ?>">

                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($p['nom']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($p['description']) ?></p>
                    <strong><?= number_format($p['prix'], 0, ',', ' ') ?> FCFA</strong>
                    <br>

                    <!-- Bouton VOIR DÉTAILS : ouvre le modal -->
                    <button class="btn btn-outline-dark mt-3 w-100 voir-details"
                        data-bs-toggle="modal"
                        data-bs-target="#modalDetailProduit">
                        <i class="bi bi-eye"></i> Voir les détails
                    </button>

                    <!-- Bouton AJOUTER AU PANIER : n'ouvre PAS le modal -->
                    <button type="button"
                        class="btn btn-dark mt-2 w-100 ajouter-panier"
                        data-id="<?= $p['id'] ?>"
                        data-nom="<?= htmlspecialchars($p['nom']) ?>"
                        data-prix="<?= $p['prix'] ?>"
                        data-image="<?= htmlspecialchars($p['image']) ?>">
                        <i class="bi bi-cart-plus"></i> Ajouter au panier
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

            <!-- LIENS VOIR PLUS -->
            <div class="text-center mt-4" id="liensVoirPlus">
                <?php foreach ($categories as $i => $cat): ?>
                    <a href="produits-<?= htmlspecialchars($cat['slug']) ?>.php"
                        class="btn btn-outline-dark voir-plus <?= $i === 0 ? '' : 'd-none' ?>"
                        data-categorie="<?= htmlspecialchars($cat['slug']) ?>">
                        Voir plus — <?= htmlspecialchars($cat['nom']) ?>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>

<!-- RESULTATS DE RECHERCHE -->
<section id="sectionRecherche" class="py-5" style="display: none;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Résultats de recherche</h2>
            <button class="btn btn-outline-dark" id="fermerRecherche">
                <i class="bi bi-x-lg"></i> Fermer
            </button>
        </div>
        <p id="texteRecherche" class="text-muted"></p>
        <div class="row g-4" id="resultatsRecherche"></div>
    </div>
</section>

<!-- PANIER -->
<section id="sectionPanier" class="py-5" style="display: none;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-cart3"></i> Mon panier</h2>
            <button class="btn btn-outline-dark" id="retourProduits">
                <i class="bi bi-arrow-left"></i> Retour aux produits
            </button>
        </div>

        <div id="contenuPanier"></div>

        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4>Total :</h4>
                    <h4 id="totalPanier">0 FCFA</h4>
                </div>
                <button class="btn btn-dark w-100 mt-3" id="validerCommande"
                    data-bs-toggle="modal" data-bs-target="#modalCommande">
                    <i class="bi bi-check-circle"></i> Passer la commande
                </button>
            </div>
        </div>
    </div>
</section>

<!-- MODAL DE COMMANDE -->
<div class="modal fade" id="modalCommande" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-bag-check"></i> Finaliser la commande
                </h5>
                <button type="button" class="btn-close btn-close-white"
                    data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <!-- Récapitulatif du panier -->
                <h6 class="text-muted mb-3">Récapitulatif de votre panier</h6>
                <div id="recapModalCommande"></div>

                <hr>

                <!-- Informations client -->
                <h6 class="text-muted mb-3">Vos informations</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nom du client <span class="text-danger">*</span></label>
                        <input type="text" id="clientNom" class="form-control"
                            placeholder="Votre nom complet" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Numéro de téléphone <span class="text-danger">*</span></label>
                        <input type="tel" id="clientNumero" class="form-control"
                            placeholder="ex: 0190890998" required>
                    </div>
                </div>

                <div id="erreurModal" class="alert alert-danger mt-3 d-none"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">
                    Annuler
                </button>
                <button type="button" class="btn btn-success" id="envoyerWhatsApp">
                    <i class="bi bi-whatsapp"></i> Envoyer la commande sur WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE DÉTAILS PRODUIT -->
<div class="modal fade" id="modalDetailProduit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="detailNomTitre">
                    <i class="bi bi-box-seam"></i> Détails du produit
                </h5>
                <button type="button" class="btn-close btn-close-white"
                    data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">

                    <!-- GALERIE D'IMAGES -->
                    <div class="col-md-6">
                        <img id="detailImagePrincipale"
                            src="" alt="Image principale"
                            class="img-fluid rounded shadow-sm"
                            style="width:100%; height:300px; object-fit:cover;">

                        <!-- Miniatures -->
                        <div id="detailMiniatures"
                            class="d-flex gap-2 mt-3 flex-wrap"></div>
                    </div>

                    <!-- INFOS -->
                    <div class="col-md-6">
                        <h3 id="detailNom" class="mb-2"></h3>

                        <h4 id="detailPrix" class="text-primary mb-3"></h4>

                        <p id="detailDescription" class="text-muted"></p>

                        <hr>

                        <div class="d-grid gap-2">
                            <button class="btn btn-dark btn-lg"
                                id="detailAjouterPanier">
                                <i class="bi bi-cart-plus"></i> Ajouter au panier
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


<script>
/* MODAL DE DÉTAILS PRODUIT */

const modalDetail = document.getElementById("modalDetailProduit");

// Quand le modal s'ouvre
modalDetail.addEventListener("show.bs.modal", function (event) {
    // Le bouton qui a déclenché le modal
    const bouton = event.relatedTarget;
    if (!bouton) return;

    // On remonte à la carte parente qui contient les data-*
    const carte = bouton.closest(".produit-carte");
    if (!carte) return;

    // Récupération des données
    const id          = carte.dataset.id;
    const nom         = carte.dataset.nom;
    const description = carte.dataset.description;
    const prix        = parseInt(carte.dataset.prix);
    const imagePrincipale = carte.dataset.image;
    let images = [];
    try {
        images = JSON.parse(carte.dataset.images || "[]");
    } catch (e) {
        images = [imagePrincipale];
    }


    // Si aucune image disponible, on met celle par défaut
    if (images.length === 0) images = [imagePrincipale];

    // Remplissage du modal
    document.getElementById("detailNomTitre").innerHTML =
        '<i class="bi bi-box-seam"></i> ' + nom;
    document.getElementById("detailNom").textContent = nom;
    document.getElementById("detailDescription").textContent = description;
    document.getElementById("detailPrix").textContent =
        prix.toLocaleString() + " FCFA";

    // Image principale
    const imgPrincipale = document.getElementById("detailImagePrincipale");
    imgPrincipale.src = images[0];

    // Miniatures
    const conteneurMini = document.getElementById("detailMiniatures");
    conteneurMini.innerHTML = "";

    if (images.length > 1) {
        images.forEach(function (src, index) {
            const mini = document.createElement("img");
            mini.src = src;
            mini.style.width = "60px";
            mini.style.height = "60px";
            mini.style.objectFit = "cover";
            mini.style.borderRadius = "6px";
            mini.style.cursor = "pointer";
            mini.style.border = index === 0 ? "2px solid #0d6efd" : "1px solid #ddd";

            mini.addEventListener("click", function () {
                imgPrincipale.src = src;
                conteneurMini.querySelectorAll("img").forEach(m => {
                    m.style.border = "1px solid #ddd";
                });
                mini.style.border = "2px solid #0d6efd";
            });

            conteneurMini.appendChild(mini);
        });
    }

    // Bouton "Ajouter au panier" du modal
    const btnAjouter = document.getElementById("detailAjouterPanier");
    btnAjouter.onclick = function () {
        // Ajout au panier (on simule le clic sur le bouton de la carte)
        const boutonOriginal = carte.querySelector(".ajouter-panier");
        if (boutonOriginal) {
            boutonOriginal.click();
        }

        // Petite confirmation visuelle
        btnAjouter.innerHTML = '<i class="bi bi-check-circle"></i> Ajouté !';
        btnAjouter.classList.remove("btn-dark");
        btnAjouter.classList.add("btn-success");

        setTimeout(function () {
            btnAjouter.innerHTML = '<i class="bi bi-cart-plus"></i> Ajouter au panier';
            btnAjouter.classList.remove("btn-success");
            btnAjouter.classList.add("btn-dark");
        }, 1500);
    };
});
</script>

<?php include 'footer.php'; ?>