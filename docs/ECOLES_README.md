# Module Écoles - Documentation

## 📋 Vue d'ensemble

Module complet de gestion des écoles suivant l'architecture DDD (Domain-Driven Design) du projet.

## 🏗️ Architecture

```
app/
├── DTOs/Ecoles/
│   └── EcoleData.php                    # Data Transfer Object
├── Exceptions/Business/
│   └── EcoleException.php               # Exception métier
├── Http/
│   ├── Controllers/
│   │   └── EcoleController.php          # Contrôleur API
│   ├── Requests/Ecoles/
│   │   ├── StoreEcoleRequest.php        # Validation création
│   │   └── UpdateEcoleRequest.php       # Validation mise à jour
│   └── Resources/
│       └── EcoleResource.php            # Transformation réponse API
├── Models/
│   └── Ecole.php                        # Modèle Eloquent
└── Services/Ecoles/
    └── EcoleService.php                 # Logique métier

database/
├── factories/
│   └── EcoleFactory.php                 # Factory pour tests
├── migrations/
│   └── 2024_01_01_000011_create_ecoles_table.php
└── seeders/
    └── EcoleSeeder.php                  # Données initiales

routes/
└── api/
    └── ecoles.php                       # Routes dédiées

tests/
└── Feature/
    └── EcoleTest.php                    # Tests fonctionnels
```

## 🎯 Fonctionnalités

### Endpoints API

**Base URL:** `/api/ecoles` (protégé par Sanctum)

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/ecoles` | Liste paginée avec filtres |
| GET | `/ecoles/actives` | Liste des écoles actives |
| GET | `/ecoles/{id}` | Détails d'une école |
| POST | `/ecoles` | Créer une école |
| PUT | `/ecoles/{id}` | Mettre à jour une école |
| DELETE | `/ecoles/{id}` | Supprimer une école |
| PATCH | `/ecoles/{id}/toggle-status` | Activer/Désactiver |

### Filtres disponibles

- `est_actif` : boolean (true/false)
- `region` : string (valeur de RegionCameroun)
- `search` : string (recherche sur libellé, code, localisation)
- `per_page` : int (pagination, défaut: 15)

### Exemple de requête

```bash
# Créer une école
curl -X POST /api/ecoles \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "code_ecole": "ENSP",
    "libelle_ecole": "École Nationale Supérieure Polytechnique",
    "region": "CENTRE",
    "localisation": "Yaoundé",
    "email_ecole": "contact@ensp.cm",
    "telephone_ecole": "+237222234567",
    "siteweb_ecole": "https://ensp.cm",
    "devise": "Excellence et Innovation",
    "bp_ecole": "BP 8390"
  }'

# Lister avec filtres
curl -X GET "/api/ecoles?est_actif=true&region=CENTRE&search=poly&per_page=10" \
  -H "Authorization: Bearer {token}"
```

## 🔒 Sécurité & Validation

### Règles de validation (Store)

- `code_ecole` : requis, unique, max 20 caractères
- `libelle_ecole` : requis, max 200 caractères
- `region` : requis, valeur de l'enum RegionCameroun
- `email_ecole` : optionnel, format email valide
- `siteweb_ecole` : optionnel, format URL valide
- `telephone_ecole` : optionnel, max 20 caractères

### Règles de validation (Update)

Mêmes règles avec `sometimes` pour les champs requis et vérification d'unicité ignorant l'ID actuel.

## 🛡️ Gestion des erreurs

Le service utilise des **transactions DB** et une **exception métier personnalisée** :

```php
try {
    $ecole = $ecoleService->create($data);
} catch (EcoleException $e) {
    // Erreur métier avec code HTTP approprié
    return api_error($e->getMessage(), null, $e->getCode());
}
```

**Codes d'erreur :**
- `404` : École non trouvée
- `422` : Validation échouée / Code déjà existant / École avec départements
- `500` : Erreur serveur

## 📊 Logging

Toutes les opérations sont loggées :
- Création d'école
- Mise à jour
- Suppression
- Changement de statut
- Erreurs avec contexte

## 🧪 Tests

```bash
# Lancer les tests du module
php artisan test --filter EcoleTest

# Avec coverage
php artisan test --filter EcoleTest --coverage
```

**Tests inclus :**
- ✅ Liste des écoles
- ✅ Création avec validation
- ✅ Affichage d'une école
- ✅ Mise à jour
- ✅ Suppression
- ✅ Toggle statut
- ✅ Liste des écoles actives
- ✅ Validation des champs requis

## 🌱 Seeders

```bash
# Peupler avec des données de test
php artisan db:seed --class=EcoleSeeder
```

Crée 3 écoles par défaut : ENSP, ENSAI, ENSET

## 🏭 Factory

```php
// Créer une école de test
Ecole::factory()->create();

// Créer une école active
Ecole::factory()->active()->create();

// Créer une école inactive
Ecole::factory()->inactive()->create();

// Créer 10 écoles
Ecole::factory()->count(10)->create();
```

## 📝 Réponses API standardisées

Utilise les helpers globaux :
- `api_success()` : Succès (200)
- `api_created()` : Création (201)
- `api_updated()` : Mise à jour (200)
- `api_deleted()` : Suppression (200)
- `api_error()` : Erreur (400+)
- `api_paginated()` : Liste paginée (200)
- `api_validation_error()` : Validation (422)

## 🔗 Relations

```php
// Récupérer une école avec ses départements
$ecole = Ecole::with('departements')->find($id);

// Dans le contrôleur (eager loading automatique)
$ecole = $ecoleService->getById($id); // charge automatiquement les départements
```

## 🚀 Utilisation dans le code

```php
use App\Services\Ecoles\EcoleService;
use App\DTOs\Ecoles\EcoleData;

// Injection de dépendance
public function __construct(private EcoleService $ecoleService) {}

// Créer une école
$data = EcoleData::from($request->validated());
$ecole = $this->ecoleService->create($data);

// Récupérer avec filtres
$ecoles = $this->ecoleService->getAll([
    'est_actif' => true,
    'region' => 'CENTRE',
    'search' => 'poly'
]);

// Toggle statut
$ecole = $this->ecoleService->toggleStatus($id);
```

## ✨ Bonnes pratiques appliquées

✅ **DTOs** avec Spatie Laravel Data  
✅ **Service Layer** avec logique métier isolée  
✅ **Transactions DB** pour l'intégrité des données  
✅ **Exception métier** personnalisée  
✅ **Validation** dans Form Requests dédiées  
✅ **API Resources** pour transformation cohérente  
✅ **Helpers de réponse** standardisés  
✅ **Logging** complet des opérations  
✅ **Tests** fonctionnels exhaustifs  
✅ **Factory & Seeder** pour développement  
✅ **Routes** organisées par module  
✅ **Documentation** complète
