# EnrolCM API

> Plateforme d'enrôlement aux concours nationaux - API Backend Laravel

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Table des Matières

- [À propos](#à-propos)
- [Démarrage Rapide](#démarrage-rapide)
- [Système d'Emails](#système-demails) ⭐ NOUVEAU
- [Migration des Services](#migration-des-services)
- [Architecture](#architecture)
- [Documentation](#documentation)
- [Modules](#modules)
- [Tests](#tests)
- [Contribution](#contribution)

## 🎯 À propos

EnrolCM est une plateforme complète de gestion des inscriptions et concours nationaux. Cette API backend Laravel fournit tous les services nécessaires pour :

- Gestion des écoles, départements, filières
- Gestion des concours et sessions
- Inscription et gestion des candidats
- Validation des documents
- Gestion des paiements
- Planification des épreuves
- Saisie et calcul des notes
- Publication des résultats

## 🚀 Démarrage Rapide

### Prérequis

- PHP 8.2+
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (pour les assets)

### Installation

```bash
# Cloner le repository
git clone <repository-url>
cd enrolcm-api

# Installer les dépendances
composer install

# Configurer l'environnement
cp .env.example .env
php artisan key:generate

# Configurer la base de données dans .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=enrolcm
# DB_USERNAME=root
# DB_PASSWORD=

# Exécuter les migrations
php artisan migrate

# (Optionnel) Seed la base de données
php artisan db:seed

# Démarrer le serveur
php artisan serve
```

L'API sera accessible sur `http://localhost:8000`

### Guide Complet

Pour un guide détaillé, consulter **[QUICK_START.md](QUICK_START.md)**

## 📧 Système d'Emails

### ⭐ Nouveau : Emails Personnalisés EnrolCM

Le système d'emails a été entièrement personnalisé avec :

- ✅ Design avec couleur emerald-600 (pas de dégradés)
- ✅ Email de bienvenue lors de l'inscription
- ✅ Email de vérification avec lien sécurisé (24h)
- ✅ Templates Blade modifiables
- ✅ Documentation complète

### Nouvelle Architecture

```
app/Services/
├── Domain/              # Logique métier
│   ├── Concours/
│   ├── Candidature/
│   ├── Examen/
│   ├── Paiement/
│   ├── Referentiel/
│   └── User/
├── Infrastructure/      # Services techniques
│   ├── Export/
│   ├── Logger/
│   ├── OCR/
│   └── QrCode/
└── Application/         # Orchestration
    ├── Stats/
    └── Dashboard/
```

## 🏗️ Architecture

### Structure du Projet

```
enrolcm-api/
├── app/
│   ├── Console/         # Commandes Artisan
│   ├── DTOs/            # Data Transfer Objects
│   ├── Enums/           # Énumérations
│   ├── Exceptions/      # Exceptions métier
│   ├── Http/
│   │   ├── Controllers/ # Contrôleurs
│   │   ├── Middleware/  # Middlewares
│   │   ├── Requests/    # Form Requests
│   │   └── Resources/   # API Resources
│   ├── Models/          # Modèles Eloquent
│   ├── Services/        # Services métier
│   └── Traits/          # Traits réutilisables
├── config/              # Configuration
├── database/
│   ├── migrations/      # Migrations
│   └── seeders/         # Seeders
├── docs/                # Documentation
├── routes/              # Routes API
├── scripts/             # Scripts de migration
└── tests/               # Tests


## 🧩 Modules

### Référentiel
- **Écoles** : Gestion des établissements
- **Départements** : Gestion des départements
- **Filières** : Gestion des filières
- **Niveaux** : Gestion des niveaux d'études
- **Matières** : Gestion des matières
- **Centres** : Gestion des centres d'examen

### Concours
- **Concours** : Gestion des concours
- **Sessions** : Gestion des sessions
- **Spécialités** : Gestion des spécialités

### Candidature
- **Candidats** : Gestion des candidats
- **Candidatures** : Gestion des candidatures
- **Documents** : Validation des documents

### Examen
- **Épreuves** : Gestion des épreuves
- **Planning** : Planification des examens
- **Notes** : Saisie et gestion des notes
- **Résultats** : Calcul et publication des résultats
- **Affectations** : Affectation des salles

### Paiement
- **Paiements** : Gestion des paiements
- **Validation** : Validation des reçus
- **OCR** : Extraction automatique des données

### Utilisateurs
- **Authentification** : Login/Logout
- **Utilisateurs** : Gestion des utilisateurs
- **Rôles** : Gestion des rôles et permissions

## 🧪 Tests

### Exécuter les Tests

```bash
# Tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter=EcoleTest

# Avec couverture
php artisan test --coverage
```

### Tests Disponibles

- Tests unitaires des services
- Tests d'intégration des contrôleurs
- Tests de validation
- Tests des relations Eloquent

### Commandes Laravel

```bash
# Nettoyer le cache
php artisan optimize:clear

# Générer l'autoload
composer dump-autoload

# Lister les routes
php artisan route:list

# Créer un contrôleur
php artisan make:controller NomController

# Créer un modèle avec migration
php artisan make:model Nom -m

# Créer un service
php artisan make:class Services/Domain/Nom/NomService
```

## 📊 API Endpoints

### Base URL

```
http://localhost:8000/api/v1
```

### Principaux Endpoints

| Module | Endpoint | Description |
|--------|----------|-------------|
| Écoles | `/ecoles` | CRUD écoles |
| Départements | `/departements` | CRUD départements |
| Filières | `/filieres` | CRUD filières |
| Niveaux | `/niveaux` | CRUD niveaux |
| Matières | `/matieres` | CRUD matières |
| Concours | `/concours` | CRUD concours |
| Candidats | `/candidats` | CRUD candidats |
| Épreuves | `/epreuves` | CRUD épreuves |
| Notes | `/notes` | Gestion des notes |
| Résultats | `/resultats` | Consultation résultats |

### Documentation API

```bash
# Lister toutes les routes
php artisan route:list

# Filtrer par préfixe
php artisan route:list --path=api/v1
```

## 🤝 Contribution

### Workflow Git

```bash
# Créer une branche
git checkout -b feature/nom-fonctionnalite

# Développer et tester
php artisan test

# Commit
git add .
git commit -m "feat: description"

# Push
git push origin feature/nom-fonctionnalite
```

### Standards de Code

- PSR-12 pour le style de code
- PHPDoc pour la documentation
- Tests unitaires obligatoires
- Validation des données avec Form Requests
- Utilisation des DTOs pour les transferts de données

### Conventions de Nommage

- **Contrôleurs** : `NomController.php`
- **Services** : `NomService.php`
- **Modèles** : `Nom.php` (singulier)
- **Migrations** : `create_noms_table.php` (pluriel)
- **Routes** : `/noms` (pluriel, kebab-case)

## 📝 Changelog

### Version 2.0 (Janvier 2026)

- ✅ Migration vers architecture par domaines
- ✅ Scripts de migration automatisés
- ✅ Documentation complète
- ✅ Nouvelle structure des services

### Version 1.0

- ✅ Modules de base (Écoles, Départements, Filières, etc.)
- ✅ Gestion des concours
- ✅ Gestion des candidatures
- ✅ Système de paiement
- ✅ Gestion des examens

## 📄 License

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 👥 Équipe

- **Backend** : Laravel API
- **Frontend** : React Dashboard (enrolcm-dashboard)
- **Web** : React  App (enrolcm-app)
