# 📚 Documentation du Projet

Bienvenue dans la documentation complète du projet de gestion des concours.

## 📖 Table des Matières

### Module Écoles

- **[Guide Utilisateur](ECOLES_README.md)** - Documentation complète pour l'utilisation du module
- **[Architecture Technique](ECOLES_ARCHITECTURE.md)** - Détails de l'architecture DDD
- **[Résumé de la Branche](ECOLES_SUMMARY.md)** - Récapitulatif des modifications
- **[Commandes Utiles](ECOLES_COMMANDES.md)** - Commandes pour le développement

### Tests

- **[Guide des Tests](TESTING.md)** - Documentation des tests du projet

## 🏗️ Architecture Globale

Le projet suit une architecture **Domain-Driven Design (DDD)** avec :

```
app/
├── DTOs/              # Data Transfer Objects
├── Enums/             # Énumérations métier
├── Exceptions/        # Exceptions personnalisées
│   └── Business/      # Exceptions métier
├── Helpers/           # Fonctions utilitaires
├── Http/
│   ├── Controllers/   # Contrôleurs API
│   ├── Requests/      # Validation des requêtes
│   └── Resources/     # Transformation des réponses
├── Models/            # Modèles Eloquent
├── Providers/         # Service Providers
└── Services/          # Logique métier
```

## 🎯 Modules Disponibles

### 1. Module Écoles
Gestion complète des établissements d'enseignement supérieur.

**Fonctionnalités :**
- CRUD complet
- Filtrage et recherche
- Gestion du statut (actif/inactif)
- Relations avec départements

**Documentation :** [ECOLES_README.md](ECOLES_README.md)

### 2. Module Candidats
Gestion des candidats aux concours.

### 3. Module Authentification
Système d'authentification avec Laravel Sanctum.

## 🛠️ Technologies

- **Framework :** Laravel 12
- **PHP :** 8.2+
- **Base de données :** MySQL/PostgreSQL
- **Authentification :** Laravel Sanctum
- **DTOs :** Spatie Laravel Data
- **Tests :** PHPUnit

## 🚀 Démarrage Rapide

```bash
# Installation
composer install
cp .env.example .env
php artisan key:generate

# Base de données
php artisan migrate
php artisan db:seed

# Tests
php artisan test

# Serveur de développement
php artisan serve
```

## 📝 Conventions de Code

### Naming Conventions

- **Controllers :** `NomController` (PascalCase)
- **Models :** `Nom` (PascalCase, singulier)
- **Services :** `NomService` (PascalCase)
- **DTOs :** `NomData` (PascalCase)
- **Requests :** `ActionNomRequest` (PascalCase)
- **Resources :** `NomResource` (PascalCase)
- **Exceptions :** `NomException` (PascalCase)

### Structure des Fichiers

```php
<?php

namespace App\Http\Controllers;

use App\Services\NomService;
use Illuminate\Http\JsonResponse;

class NomController extends Controller
{
    public function __construct(
        private NomService $nomService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $data = $this->nomService->getAll();
            return api_success($data);
        } catch (Exception $e) {
            return api_error($e->getMessage());
        }
    }
}
```

## 🧪 Tests

### Lancer les tests

```bash
# Tous les tests
php artisan test

# Tests d'un module spécifique
php artisan test --filter EcoleTest

# Tests avec coverage
php artisan test --coverage
```

### Structure des tests

```
tests/
├── Feature/           # Tests fonctionnels
│   ├── EcoleTest.php
│   └── ...
└── Unit/             # Tests unitaires
```

## 📊 Helpers de Réponse API

Le projet utilise des helpers standardisés pour les réponses API :

```php
// Succès
api_success($data, 'Message', 200)
api_created($data, 'Créé')
api_updated($data, 'Mis à jour')
api_deleted('Supprimé')

// Erreurs
api_error('Message', $errors, 400)
api_not_found('Non trouvé')
api_unauthorized('Non autorisé')
api_forbidden('Accès interdit')
api_validation_error($errors, 'Validation')

// Pagination
api_paginated($paginatedData, 'Message')
```

## 🔒 Sécurité

- Toutes les routes API sont protégées par **Laravel Sanctum**
- Validation stricte avec **Form Requests**
- Transactions DB pour l'intégrité des données
- Logging complet des opérations
- Gestion d'erreurs avec exceptions métier

## 📈 Bonnes Pratiques

### 1. Utiliser les DTOs
```php
$data = NomData::from($request->validated());
$result = $this->service->create($data);
```

### 2. Transactions DB
```php
DB::transaction(function () {
    // Opérations atomiques
});
```

### 3. Gestion d'erreurs
```php
try {
    // Code
} catch (BusinessException $e) {
    return api_error($e->getMessage(), null, $e->getCode());
}
```

### 4. Eager Loading
```php
$ecoles = Ecole::with('departements')->get();
```

### 5. API Resources
```php
return api_success(
    NomResource::collection($items),
    'Message'
);
```

## 🔗 Liens Utiles

- [Laravel Documentation](https://laravel.com/docs)
- [Spatie Laravel Data](https://spatie.be/docs/laravel-data)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [PHPUnit](https://phpunit.de/documentation.html)

## 🤝 Contribution

Pour contribuer au projet :

1. Créer une branche feature : `git checkout -b feature/nom-module`
2. Suivre l'architecture DDD existante
3. Écrire des tests
4. Documenter les changements
5. Créer une Pull Request

## 📞 Support

Pour toute question ou problème, consultez la documentation spécifique de chaque module ou contactez l'équipe de développement.

---

**Dernière mise à jour :** Décembre 2024
