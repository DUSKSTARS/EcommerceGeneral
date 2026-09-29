# Passpartout

Projet de boutique en ligne développé en PHP/MySQL avec Bootstrap et JavaScript.

## Description

Passpartout est une petite application e-commerce simple permettant :

- d'afficher des produits par catégorie ;
- de filtrer les produits ;
- de rechercher un produit ;
- d'ajouter des articles au panier ;
- de gérer une partie administration pour les catégories et les produits ;
- de se connecter en tant qu'administrateur pour gérer le contenu.

## Fonctionnalités

- Page d'accueil avec bannière et présentation produit ;
- Liste des catégories dynamiques ;
- Affichage des produits avec prix et image ;
- Panier côté client avec mise à jour en JavaScript ;
- Recherche rapide des produits ;
- Interface d'administration pour gérer les produits et catégories ;
- Authentification admin avec session PHP.

## Stack technique

- PHP 8+
- MySQL
- Bootstrap 5
- JavaScript
- HTML / CSS

## Prérequis

Avant de lancer le projet, il faut avoir installé :

- XAMPP / WAMP / MAMP
- Apache + MySQL activés
- Un navigateur web

## Installation

1. Placez le dossier du projet dans le répertoire web de votre environnement local, par exemple :

   - XAMPP : `C:/xampp/htdocs/passpartout`

2. Créez la base de données MySQL.

3. Importez le fichier SQL suivant :

   - `database.sql`

4. Vérifiez la configuration de connexion dans :

   - `config/database.php`

   Exemple de configuration :

   ```php
   $host   = 'localhost';
   $dbname = 'ma_boutique';
   $user   = 'root';
   $pass   = '';
   ```

5. Démarrez Apache et MySQL.

6. Ouvrez le projet dans le navigateur :

   - `http://localhost/passpartout/`

## Accès administration

Pour accéder à l'espace admin :

- URL : `http://localhost/passpartout/login.php`
- Email : `admin@boutique.com`
- Mot de passe : `admin123`

## Structure du projet

```text
passpartout/
├── admin/
│   ├── categorie_ajouter.php
│   ├── categorie_modifier.php
│   ├── categorie_supprimer.php
│   ├── categories.php
│   ├── index.php
│   ├── produit_ajouter.php
│   ├── produit_modifier.php
│   ├── produit_supprimer.php
│   └── produits.php
├── assets/
│   ├── css/
│   └── js/
├── bootstrap-5.2.3-dist/
├── bootstrap-icons/
├── config/
│   └── database.php
├── includes/
│   ├── auth.php
│   ├── footer.php
│   └── header.php
├── images/
├── ajouter_panier.php
├── creer_admin.php
├── database.sql
├── index.php
├── jquery-3.7.0.js
├── login.php
├── logout.php
├── recherche.php
├── style.css
└── README.md
```

## Notes

- Le projet est pensé comme un mini site commercial fonctionnel.
- Il peut être étendu avec des commandes réelles, paiement, gestion des stocks etc.
- Les données de démonstration sont incluses dans le fichier `database.sql`.

## Auteur

Projet de boutique simple pour démonstration et apprentissage en PHP.
