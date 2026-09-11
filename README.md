# Mille Sabords

Mille Sabords est une fausse boutique en ligne de mangas développée en PHP. Le site permet de parcourir le catalogue, consulter les mangas, gérer un panier et suivre ses commandes.

## Fonctionnalités

- Consultation et recherche du catalogue de mangas
- Création de compte et connexion
- Gestion du panier
- Passage et suivi des commandes
- Génération de factures
- Avis sur les mangas
- Interface d'administration pour les mangas et les commandes
- Paiement Stripe prévu dans l'application

## Technologies

- PHP
- MySQL
- HTML, CSS et JavaScript
- PDO
- Stripe PHP
- Dompdf

## Installation

### Prérequis

- PHP 8 ou version supérieure
- MySQL
- Apache ou le serveur web intégré de PHP

### Configuration

1. Clonez le projet et placez-le dans le dossier servi par votre serveur web.
2. Créez la base de données MySQL utilisée par l'application.
3. Complétez `config.local.php` avec vos informations locales :

```php
<?php
return [
	'client-stripe' => 'votre_cle_stripe',
	'DB-host' => 'localhost',
	'DB-name' => 'nom_de_la_base',
	'DB-user' => 'utilisateur',
	'DB-pass' => 'mot_de_passe',
];
```

Le fichier `config.local.php` contient des informations sensibles et ne doit pas être publié.

## Structure du projet

```text
API/          Endpoints de l'application
controllers/  Contrôleurs
core/         Routeur, connexion à la base et classes de base
models/       Modèles liés aux données
public/       CSS, JavaScript, images et ressources publiques
views/        Vues PHP
index.php     Point d'entrée de l'application
```
