/* PANIER */

let panier = [];

const boutonsAjouter = document.querySelectorAll(".ajouter-panier");

boutonsAjouter.forEach(function (bouton) {
    bouton.addEventListener("click", function (event) {
        // Empêche l'ouverture du modal quand on clique sur "Ajouter au panier"
        event.stopPropagation();

        const id = this.dataset.id;
        const nom = this.dataset.nom;
        const prix = parseInt(this.dataset.prix);
        const image = this.dataset.image;

        const produitExistant = panier.find(p => p.id === id);

        if (produitExistant) {
            produitExistant.quantite++;
            afficherNotification("Quantité augmentée : " + nom, "info");
        } else {
            panier.push({ id, nom, prix, image, quantite: 1 });
            afficherNotification(nom + " ajouté au panier", "success");
        }

        mettreAJourPanier();
        mettreAJourCompteur();
        animerCompteur();
    });
});

/* Notification toast (Bootstrap toasts dynamiques) */
function afficherNotification(message, type = "success") {
    // Crée le conteneur s'il n'existe pas
    let conteneur = document.getElementById("toastConteneur");
    if (!conteneur) {
        conteneur = document.createElement("div");
        conteneur.id = "toastConteneur";
        conteneur.className = "toast-container position-fixed bottom-0 end-0 p-3";
        conteneur.style.zIndex = "9999";
        document.body.appendChild(conteneur);
    }

    const icone = type === "success" ? "check-circle" : "info-circle";
    const bg    = type === "success" ? "success"     : "dark";

    const toastEl = document.createElement("div");
    toastEl.className = `toast align-items-center text-white bg-${bg} border-0`;
    toastEl.setAttribute("role", "alert");
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-${icone}"></i> ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"></button>
        </div>`;

    conteneur.appendChild(toastEl);

    const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
    toast.show();

    // Supprime du DOM après disparition
    toastEl.addEventListener("hidden.bs.toast", () => toastEl.remove());
}

/* Animation du compteur panier */
function animerCompteur() {
    const compteur = document.getElementById("compteurPanier");
    if (!compteur) return;
    compteur.classList.add("pulse");
    setTimeout(() => compteur.classList.remove("pulse"), 400);
}

function mettreAJourCompteur() {
    let totalQuantite = 0;
    panier.forEach(p => totalQuantite += p.quantite);
    document.getElementById("compteurPanier").textContent = totalQuantite;
}

function mettreAJourPanier() {
    const contenuPanier = document.getElementById("contenuPanier");
    const totalPanier = document.getElementById("totalPanier");

    contenuPanier.innerHTML = "";
    let total = 0;

    if (panier.length === 0) {
        contenuPanier.innerHTML = `
            <div class="alert alert-info text-center">
                <i class="bi bi-cart-x fs-1"></i>
                <h4 class="mt-3">Votre panier est vide</h4>
                <p>Ajoutez des produits pour les retrouver ici.</p>
            </div>`;
        totalPanier.textContent = "0 FCFA";
        return;
    }

    panier.forEach(function (produit) {
        const sousTotal = produit.prix * produit.quantite;
        total += sousTotal;

        contenuPanier.innerHTML += `
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3 col-md-2">
                            <img src="${produit.image}" class="img-fluid rounded" alt="${produit.nom}">
                        </div>
                        <div class="col-9 col-md-3">
                            <h5>${produit.nom}</h5>
                            <p class="mb-0">${produit.prix.toLocaleString()} FCFA</p>
                        </div>
                        <div class="col-12 col-md-3 mt-3 mt-md-0">
                            <div class="input-group">
                                <button class="btn btn-outline-dark diminuer" data-id="${produit.id}">−</button>
                                <span class="input-group-text">${produit.quantite}</span>
                                <button class="btn btn-outline-dark augmenter" data-id="${produit.id}">+</button>
                            </div>
                        </div>
                        <div class="col-8 col-md-2 mt-3 mt-md-0">
                            <strong>${sousTotal.toLocaleString()} FCFA</strong>
                        </div>
                        <div class="col-4 col-md-2 mt-3 mt-md-0 text-end">
                            <button class="btn btn-danger supprimer" data-id="${produit.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;
    });

    totalPanier.textContent = total.toLocaleString() + " FCFA";

    document.querySelectorAll(".diminuer").forEach(b => {
        b.addEventListener("click", function () {
            const id = this.dataset.id;
            const produit = panier.find(p => p.id === id);
            if (produit) {
                if (produit.quantite > 1) produit.quantite--;
                else panier = panier.filter(p => p.id !== id);
            }
            mettreAJourPanier();
            mettreAJourCompteur();
        });
    });

    document.querySelectorAll(".augmenter").forEach(b => {
        b.addEventListener("click", function () {
            const id = this.dataset.id;
            const produit = panier.find(p => p.id === id);
            if (produit) produit.quantite++;
            mettreAJourPanier();
            mettreAJourCompteur();
        });
    });

    document.querySelectorAll(".supprimer").forEach(b => {
        b.addEventListener("click", function () {
            const id = this.dataset.id;
            panier = panier.filter(p => p.id !== id);
            mettreAJourPanier();
            mettreAJourCompteur();
        });
    });
}

/* AFFICHER LE PANIER */

document.getElementById("boutonPanier").addEventListener("click", function (e) {
    e.preventDefault();
    document.getElementById("contenuBoutique").style.display = "none";
    document.querySelector(".hero").style.display = "none";
    document.querySelector(".espace").style.display = "none";
    document.getElementById("sectionPanier").style.display = "block";
    mettreAJourPanier();
    window.scrollTo({ top: 0, behavior: "smooth" });
});

/* RETOUR AUX PRODUITS */

document.getElementById("retourProduits").addEventListener("click", function () {
    document.getElementById("contenuBoutique").style.display = "block";
    document.querySelector(".hero").style.display = "block";
    document.querySelector(".espace").style.display = "block";
    document.getElementById("sectionPanier").style.display = "none";
    document.getElementById("produits").scrollIntoView({ behavior: "smooth" });
});

/* FILTRE PAR CATÉGORIES */

const boutonsFiltre = document.querySelectorAll(".filtre-btn");
const produits = document.querySelectorAll(".produit");
const liensVoirPlus = document.querySelectorAll(".voir-plus");

function afficherCategorie(categorie) {
    let nombreAffiche = 0;

    produits.forEach(function (produit) {
        const cat = produit.dataset.categorie;
        if (cat === categorie && nombreAffiche < 6) {
            produit.style.display = "";
            nombreAffiche++;
        } else {
            produit.style.display = "none";
        }
    });

    liensVoirPlus.forEach(function (lien) {
        if (lien.dataset.categorie === categorie) lien.classList.remove("d-none");
        else lien.classList.add("d-none");
    });
}

boutonsFiltre.forEach(function (bouton) {
    bouton.addEventListener("click", function () {
        const categorie = this.dataset.categorie;

        boutonsFiltre.forEach(function (btn) {
            btn.classList.remove("active", "btn-dark");
            btn.classList.add("btn-outline-dark");
        });

        this.classList.add("active", "btn-dark");
        this.classList.remove("btn-outline-dark");

        afficherCategorie(categorie);
    });
});

/* RECHERCHE */

const champRecherche      = document.getElementById("champRecherche");
const formRecherche       = document.getElementById("formRecherche");
const sectionRecherche    = document.getElementById("sectionRecherche");
const resultatsRecherche  = document.getElementById("resultatsRecherche");
const texteRecherche      = document.getElementById("texteRecherche");
const fermerRecherche     = document.getElementById("fermerRecherche");

function normaliserTexte(texte) {
    return texte.toLowerCase().normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "").trim();
}

function rechercherProduit() {
    const recherche = normaliserTexte(champRecherche.value);

    if (recherche === "") {
        sectionRecherche.style.display = "none";
        document.getElementById("contenuBoutique").style.display = "block";
        document.querySelector(".hero").style.display = "block";
        document.querySelector(".espace").style.display = "block";
        return;
    }

    document.getElementById("contenuBoutique").style.display = "none";
    document.querySelector(".hero").style.display = "none";
    document.querySelector(".espace").style.display = "none";

    sectionRecherche.style.display = "block";
    resultatsRecherche.innerHTML = "";
    let nombreResultats = 0;

    produits.forEach(function (produit) {
        const titre = produit.querySelector(".card-title");
        if (!titre) return;
        const nomProduit = normaliserTexte(titre.textContent);

        if (nomProduit.includes(recherche)) {
            const copie = produit.cloneNode(true);
            copie.style.display = "";
            resultatsRecherche.appendChild(copie);
            nombreResultats++;
        }
    });

    if (nombreResultats === 0) {
        texteRecherche.textContent = "Aucun produit trouvé pour « " + champRecherche.value + " ».";
        resultatsRecherche.innerHTML = `
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="bi bi-search fs-1"></i>
                    <h4 class="mt-3">Aucun produit trouvé</h4>
                    <p class="mb-0">Essayez avec un autre nom de produit.</p>
                </div>
            </div>`;
    } else {
        texteRecherche.textContent = nombreResultats +
            (nombreResultats > 1 ? " produits trouvés pour « " : " produit trouvé pour « ") +
            champRecherche.value + " ».";
    }
}

champRecherche.addEventListener("input", rechercherProduit);
formRecherche.addEventListener("submit", e => { e.preventDefault(); rechercherProduit(); });

fermerRecherche.addEventListener("click", function () {
    champRecherche.value = "";
    rechercherProduit();
    window.scrollTo({ top: 0, behavior: "smooth" });
});

champRecherche.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        champRecherche.value = "";
        rechercherProduit();
    }
});

/* INITIALISATION */

// Détecte automatiquement la catégorie active (celle avec .active)
(function initCategorieActive() {
    const boutonActif = document.querySelector(".filtre-btn.active");
    if (boutonActif) {
        afficherCategorie(boutonActif.dataset.categorie);
    } else if (boutonsFiltre.length > 0) {
        // Aucun .active trouvé → on active le premier
        boutonsFiltre[0].classList.add("active", "btn-dark");
        boutonsFiltre[0].classList.remove("btn-outline-dark");
        afficherCategorie(boutonsFiltre[0].dataset.categorie);
    }
})();

/* MODAL DE COMMANDE + WHATSAPP */

// ⚠️ Numéro WhatsApp du vendeur (format international sans + ni espaces)
// 229 = indicatif du Bénin + 0190890998
const NUMERO_WHATSAPP = "2290190890998";

const modalCommande      = document.getElementById("modalCommande");
const recapModalCommande = document.getElementById("recapModalCommande");
const clientNom          = document.getElementById("clientNom");
const clientNumero       = document.getElementById("clientNumero");
const erreurModal        = document.getElementById("erreurModal");
const envoyerWhatsApp    = document.getElementById("envoyerWhatsApp");

/* Remplir le récapitulatif à l'ouverture du modal */
if (modalCommande) {

    modalCommande.addEventListener("show.bs.modal", function () {
        erreurModal.classList.add("d-none");

        if (panier.length === 0) {
            recapModalCommande.innerHTML = `
                <div class="alert alert-warning text-center mb-0">
                    <i class="bi bi-cart-x"></i> Votre panier est vide.
                </div>`;
            envoyerWhatsApp.disabled = true;
            return;
        }

        envoyerWhatsApp.disabled = false;

        let html = `<div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Produit</th>
                        <th class="text-center">Qté</th>
                        <th class="text-end">Prix</th>
                        <th class="text-end">Sous-total</th>
                    </tr>
                </thead>
                <tbody>`;

        let total = 0;

        panier.forEach(function (p) {
            const sousTotal = p.prix * p.quantite;
            total += sousTotal;

            html += `
                <tr>
                    <td>
                        <img src="${p.image}" alt="${p.nom}"
                            style="width:40px;height:40px;object-fit:cover;border-radius:6px;margin-right:8px;">
                        ${p.nom}
                    </td>
                    <td class="text-center">${p.quantite}</td>
                    <td class="text-end">${p.prix.toLocaleString()} FCFA</td>
                    <td class="text-end"><strong>${sousTotal.toLocaleString()} FCFA</strong></td>
                </tr>`;
        });

        html += `</tbody>
            <tfoot>
                <tr class="table-dark">
                    <td colspan="3" class="text-end"><strong>TOTAL</strong></td>
                    <td class="text-end"><strong>${total.toLocaleString()} FCFA</strong></td>
                </tr>
            </tfoot>
        </table></div>`;

        recapModalCommande.innerHTML = html;
    });

    /* Envoi de la commande sur WhatsApp */
    envoyerWhatsApp.addEventListener("click", function () {
        const nom     = clientNom.value.trim();
        const numero  = clientNumero.value.trim();

        // Vérifications
        if (nom === "" || numero === "") {
            erreurModal.textContent = "Veuillez remplir votre nom et votre numéro de téléphone.";
            erreurModal.classList.remove("d-none");
            return;
        }

        if (panier.length === 0) {
            erreurModal.textContent = "Votre panier est vide.";
            erreurModal.classList.remove("d-none");
            return;
        }

        erreurModal.classList.add("d-none");


        // 1. Enregistrement côté serveur (optionnel)

        fetch("ajouter_panier.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                panier: panier,
                client: { nom: nom, contact: numero }
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.succes) {
                console.log("Commande enregistrée, ID :", data.commande_id);
            } else {
                console.warn("Réponse serveur :", data);
            }
        })
        .catch(err => console.warn("Enregistrement échoué :", err));


        // 2. Construction du message WhatsApp

        let message = "NOUVELLE COMMANDE%0A%0A";
        message    += "Client : " + encodeURIComponent(nom) + "%0A";
        message    += "Téléphone : " + encodeURIComponent(numero) + "%0A%0A";
        message    += "Produits commandés :%0A";

        let total = 0;

        panier.forEach(function (p, index) {
            const sousTotal = p.prix * p.quantite;
            total += sousTotal;

            message += (index + 1) + ". " + encodeURIComponent(p.nom) +
                       " x" + p.quantite +
                       " = " + sousTotal.toLocaleString() + " FCFA%0A";
        });

        message += "%0A TOTAL : " + total.toLocaleString() + " FCFA%0A%0A";
        message += "Merci de confirmer ma commande";


        // 3. Ouverture de WhatsApp

        const url = "https://wa.me/290197196984" + NUMERO_WHATSAPP + "?text=" + message;
        window.open(url, "_blank");


        // 4. Fermeture du modal

        const modalInstance = bootstrap.Modal.getInstance(modalCommande);
        if (modalInstance) modalInstance.hide();
    });
}