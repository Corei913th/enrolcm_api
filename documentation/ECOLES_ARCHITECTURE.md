# Architecture du Module Écoles

## 🎯 Objectif

Module professionnel de gestion des écoles suivant strictement l'architecture DDD du projet, avec DTOs, Services, Exceptions métier, et gestion complète des erreurs.

## 📐 Principes Architecturaux

### 1. **Séparation des Responsabilités**

```
Controller → Request → DTO → Service → Model
     ↓                           ↓
  Resource ← Exception ← Transaction
```

- **Controller** : Orchestration et réponses HTTP
- **Request** : Validation des entrées
- **DTO** : Transfert de données typées
- **Service** : Logique métier et transactions
- **Model** : Accès aux données
- **Resource** : Transformation des sorties
- **Exception** : Gestion des erreurs métier

### 2. **Flux de Données**

#### Création d'une école

```
1. Client → POST /api/ecoles + JSON
2. StoreEcoleRequest → Validation
3. EcoleData::from() → DTO typé
4. EcoleService::create() → Transaction DB
5. Ecole::create() → Insertion
6. EcoleResource → Transformation
7. api_created() → Réponse JSON standardisée
```

#### Gestion d'erreur

```
1. Service détecte une erreur
2. throw new EcoleException('message', code)
3. Controller catch l'exception
4. api_error() → Réponse JSON d'erreur
5. Log::error() → Traçabilité
```

## 🏛️ Structure des Fichiers

### DTOs (Data Transfer Objects)

**Fichier :** `app/DTOs/Ecoles/EcoleData.php`

```php
use Spatie\LaravelData\Data;

class EcoleData extends Data
{
    public function __construct(
        public string $code_ecole,
        public string $libelle_ecole,
        public ?string $region,
        // ... autres propriétés
    ) {}
}
```

**Avantages :**
- Typage fort
- Validation automatique
- Transformation facile
- Immutabilité

### Services

**Fichier :** `app/Services/Ecoles/EcoleService.php`

**Responsabilités :**
- Logique métier complexe
- Transactions DB
- Gestion des erreurs métier
- Logging des opérations
- Vérifications de cohérence

**Méthodes :**
- `getAll(array $filters)` : Liste avec filtres
- `getById(string $id)` : Récupération unique
- `create(EcoleData $data)` : Création avec transaction
- `update(string $id, EcoleData $data)` : Mise à jour
- `delete(string $id)` : Suppression avec vérifications
- `toggleStatus(string $id)` : Changement de statut
- `getActive()` : Liste des actives

**Pattern utilisé :**

```php
public function create(EcoleData $data): Ecole
{
    try {
        return DB::transaction(function () use ($data) {
            // Vérifications métier
            if (Ecole::where('code_ecole', $data->code_ecole)->exists()) {
                throw new EcoleException('Code déjà existant', 422);
            }

            // Création
            $ecole = Ecole::create($data->toArray());

            // Logging
            Log::info('École créée', ['id' => $ecole->id]);

            return $ecole->load('departements');
        });
    } catch (EcoleException $e) {
        throw $e; // Propager les exceptions métier
    } catch (\Exception $e) {
        Log::error('Erreur création école', ['error' => $e->getMessage()]);
        throw new EcoleException('Impossible de créer l\'école');
    }
}
```

### Exceptions Métier

**Fichier :** `app/Exceptions/Business/EcoleException.php`

```php
class EcoleException extends Exception
{
    public function __construct(string $message = "Erreur liée aux écoles", int $code = 500)
    {
        parent::__construct($message);
        $this->code = $code;
    }

    public function render()
    {
        return api_error($this->getMessage(), null, $this->code);
    }
}
```

**Codes HTTP utilisés :**
- `404` : Ressource non trouvée
- `422` : Erreur de validation métier
- `500` : Erreur serveur

### Form Requests

**Fichiers :**
- `app/Http/Requests/Ecoles/StoreEcoleRequest.php`
- `app/Http/Requests/Ecoles/UpdateEcoleRequest.php`

**Responsabilités :**
- Validation des entrées
- Messages d'erreur personnalisés
- Autorisation
- Réponse d'erreur standardisée

**Pattern :**

```php
protected function failedValidation(Validator $validator)
{
    throw new HttpResponseException(
        api_validation_error($validator->errors(), 'Erreur de validation')
    );
}
```

### API Resources

**Fichier :** `app/Http/Resources/EcoleResource.php`

**Responsabilités :**
- Transformation des données
- Formatage des dates
- Inclusion conditionnelle des relations
- Labels des enums

```php
public function toArray($request): array
{
    return [
        'id' => $this->id,
        'code_ecole' => $this->code_ecole,
        'region_label' => $this->region ? RegionCameroun::label($this->region) : null,
        'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        
        // Relations conditionnelles
        'departements' => DepartementResource::collection($this->whenLoaded('departements')),
        'departements_count' => $this->when(
            $this->relationLoaded('departements'),
            fn() => $this->departements->count()
        ),
    ];
}
```

### Controllers

**Fichier :** `app/Http/Controllers/EcoleController.php`

**Responsabilités :**
- Orchestration
- Injection de dépendances
- Gestion des réponses HTTP
- Catch des exceptions

**Pattern :**

```php
public function store(StoreEcoleRequest $request): JsonResponse
{
    try {
        $ecoleData = EcoleData::from($request->validated());
        $ecole = $this->ecoleService->create($ecoleData);

        return api_created(
            new EcoleResource($ecole),
            'École créée avec succès'
        );
    } catch (EcoleException $e) {
        return api_error($e->getMessage(), null, $e->getCode());
    }
}
```

## 🔄 Transactions DB

**Toutes les opérations d'écriture utilisent des transactions :**

```php
DB::transaction(function () {
    // Opérations atomiques
    // Si erreur → rollback automatique
});
```

**Avantages :**
- Intégrité des données
- Rollback automatique en cas d'erreur
- Cohérence garantie

## 📝 Logging

**Chaque opération est loggée :**

```php
// Succès
Log::info('École créée avec succès', [
    'ecole_id' => $ecole->id,
    'code_ecole' => $ecole->code_ecole
]);

// Erreur
Log::error('Erreur lors de la création de l\'école', [
    'error' => $e->getMessage(),
    'data' => $data->toArray()
]);
```

## 🎨 Helpers de Réponse

**Utilisation systématique des helpers globaux :**

```php
// Succès
api_success($data, 'Message', 200)
api_created($data, 'Créé')
api_updated($data, 'Mis à jour')
api_deleted('Supprimé')

// Erreurs
api_error('Message', $errors, 400)
api_not_found('Non trouvé')
api_validation_error($errors, 'Validation')

// Pagination
api_paginated($paginatedData, 'Message')
```

**Format de réponse standardisé :**

```json
{
  "success": true,
  "message": "École créée avec succès",
  "data": {
    "id": "uuid",
    "code_ecole": "ENSP",
    "libelle_ecole": "École Nationale..."
  }
}
```

## 🧪 Tests

**Fichier :** `tests/Feature/EcoleTest.php`

**Couverture :**
- ✅ CRUD complet
- ✅ Validation
- ✅ Filtres et recherche
- ✅ Toggle statut
- ✅ Liste des actives
- ✅ Gestion des erreurs

**Pattern :**

```php
public function it_can_create_ecole()
{
    $data = ['code_ecole' => 'TEST', ...];

    $response = $this->actingAs($this->user)
        ->postJson('/api/ecoles', $data);

    $response->assertStatus(201)
        ->assertJsonStructure(['success', 'message', 'data']);

    $this->assertDatabaseHas('ecoles', ['code_ecole' => 'TEST']);
}
```

## 🚀 Routes Organisées

**Fichier :** `routes/api/ecoles.php`

```php
Route::middleware('auth:sanctum')->prefix('ecoles')->group(function () {
    Route::get('actives', [EcoleController::class, 'active']);
    Route::patch('{ecole}/toggle-status', [EcoleController::class, 'toggleStatus']);
    
    // CRUD
    Route::get('/', [EcoleController::class, 'index']);
    Route::post('/', [EcoleController::class, 'store']);
    Route::get('{ecole}', [EcoleController::class, 'show']);
    Route::put('{ecole}', [EcoleController::class, 'update']);
    Route::delete('{ecole}', [EcoleController::class, 'destroy']);
});
```

**Inclusion dans api.php :**

```php
Route::middleware('auth:sanctum')->group(function () {
    require __DIR__.'/api/ecoles.php';
});
```

## 📊 Factory & Seeder

### Factory

**Fichier :** `database/factories/EcoleFactory.php`

```php
public function definition(): array
{
    return [
        'code_ecole' => strtoupper($this->faker->unique()->lexify('???')),
        'libelle_ecole' => $this->faker->company() . ' - École Supérieure',
        'region' => $this->faker->randomElement(RegionCameroun::values()),
        'est_actif' => $this->faker->boolean(90),
    ];
}

public function active(): static
{
    return $this->state(fn (array $attributes) => ['est_actif' => true]);
}
```

### Seeder

**Fichier :** `database/seeders/EcoleSeeder.php`

Crée 3 écoles réelles du Cameroun : ENSP, ENSAI, ENSET

## ✅ Checklist d'Implémentation

- [x] DTO avec Spatie Laravel Data
- [x] Service avec logique métier
- [x] Transactions DB
- [x] Exception métier personnalisée
- [x] Form Requests avec validation
- [x] API Resource pour transformation
- [x] Helpers de réponse standardisés
- [x] Logging complet
- [x] Tests fonctionnels
- [x] Factory pour tests
- [x] Seeder avec données réelles
- [x] Routes organisées par module
- [x] Documentation complète
- [x] Gestion d'erreurs robuste
- [x] Eager loading des relations
- [x] Filtres et recherche
- [x] Pagination

## 🎓 Bonnes Pratiques Appliquées

1. **Single Responsibility Principle** : Chaque classe a une responsabilité unique
2. **Dependency Injection** : Services injectés dans les contrôleurs
3. **Type Safety** : DTOs typés avec Spatie Laravel Data
4. **Error Handling** : Exceptions métier avec codes HTTP appropriés
5. **Transaction Management** : Toutes les écritures dans des transactions
6. **Logging** : Traçabilité complète des opérations
7. **Testing** : Tests fonctionnels exhaustifs
8. **Documentation** : Code auto-documenté et README complet
9. **Standardization** : Helpers de réponse uniformes
10. **Separation of Concerns** : Routes, validation, logique métier séparées

## 🔗 Intégration avec le Reste du Projet

Le module Écoles s'intègre parfaitement avec :
- **Départements** : Relation `hasMany`
- **Authentification** : Protection Sanctum
- **Helpers** : Utilisation des helpers globaux
- **Enums** : RegionCameroun
- **Logging** : Système centralisé
- **Tests** : Suite de tests du projet

Cette architecture garantit maintenabilité, testabilité et évolutivité du module.
