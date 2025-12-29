# 🧪 Guide des Tests

Documentation complète pour les tests du projet.

## 📋 Vue d'ensemble

Le projet utilise **PHPUnit** pour les tests avec une couverture complète des fonctionnalités.

## 🏗️ Structure des Tests

```
tests/
├── Feature/              # Tests fonctionnels (API, intégration)
│   ├── Auth/
│   ├── EcoleTest.php
│   └── ...
├── Unit/                 # Tests unitaires (logique métier)
│   └── ...
└── TestCase.php         # Classe de base pour les tests
```

## 🎯 Types de Tests

### 1. Tests Fonctionnels (Feature)

Tests end-to-end qui vérifient le comportement complet de l'application.

**Exemple : EcoleTest.php**

```php
<?php

namespace Tests\Feature;

use App\Models\Ecole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_ecoles()
    {
        Ecole::factory()->count(5)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/ecoles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_create_ecole()
    {
        $data = [
            'code_ecole' => 'TEST',
            'libelle_ecole' => 'École de Test',
            'region' => 'CENTRE',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/ecoles', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('ecoles', ['code_ecole' => 'TEST']);
    }
}
```

### 2. Tests Unitaires (Unit)

Tests isolés de la logique métier.

```php
<?php

namespace Tests\Unit;

use App\Services\Ecoles\EcoleService;
use Tests\TestCase;

class EcoleServiceTest extends TestCase
{
    /** @test */
    public function it_validates_unique_code()
    {
        // Test de la logique métier
    }
}
```

## 🚀 Commandes de Test

### Lancer tous les tests

```bash
php artisan test
```

### Lancer un fichier de test spécifique

```bash
php artisan test tests/Feature/EcoleTest.php
```

### Lancer un test spécifique

```bash
php artisan test --filter it_can_create_ecole
```

### Lancer les tests d'un module

```bash
php artisan test --filter EcoleTest
```

### Tests avec coverage

```bash
php artisan test --coverage
```

### Tests avec coverage minimum

```bash
php artisan test --coverage --min=80
```

### Tests en mode verbose

```bash
php artisan test -v
```

### Tests en parallèle

```bash
php artisan test --parallel
```

## 📊 Assertions Courantes

### Assertions HTTP

```php
// Status codes
$response->assertStatus(200);
$response->assertOk();
$response->assertCreated();
$response->assertNoContent();
$response->assertNotFound();
$response->assertForbidden();
$response->assertUnauthorized();

// JSON structure
$response->assertJsonStructure([
    'success',
    'data' => [
        'id',
        'name'
    ]
]);

// JSON content
$response->assertJson([
    'success' => true,
    'data' => ['name' => 'Test']
]);

// JSON path
$response->assertJsonPath('data.name', 'Test');

// JSON count
$response->assertJsonCount(5, 'data');
```

### Assertions Base de Données

```php
// Vérifier la présence
$this->assertDatabaseHas('ecoles', [
    'code_ecole' => 'TEST'
]);

// Vérifier l'absence
$this->assertDatabaseMissing('ecoles', [
    'code_ecole' => 'TEST'
]);

// Compter les enregistrements
$this->assertDatabaseCount('ecoles', 5);
```

### Assertions de Validation

```php
$response->assertJsonValidationErrors(['code_ecole', 'libelle_ecole']);
$response->assertJsonValidationErrorFor('code_ecole');
```

## 🏭 Factories

Les factories permettent de générer des données de test facilement.

### Utilisation

```php
// Créer un modèle
$ecole = Ecole::factory()->create();

// Créer plusieurs modèles
$ecoles = Ecole::factory()->count(10)->create();

// Créer avec des attributs spécifiques
$ecole = Ecole::factory()->create([
    'code_ecole' => 'CUSTOM',
    'est_actif' => true
]);

// Utiliser des states
$ecole = Ecole::factory()->active()->create();
$ecole = Ecole::factory()->inactive()->create();

// Créer sans sauvegarder
$ecole = Ecole::factory()->make();
```

### Définir une Factory

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EcoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code_ecole' => $this->faker->unique()->lexify('???'),
            'libelle_ecole' => $this->faker->company(),
            'est_actif' => true,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }
}
```

## 🔄 RefreshDatabase

Le trait `RefreshDatabase` réinitialise la base de données entre chaque test.

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class EcoleTest extends TestCase
{
    use RefreshDatabase;

    // Les tests auront une base de données propre
}
```

## 🎭 Mocking

### Mocker un service

```php
use Mockery;

public function test_with_mock()
{
    $mock = Mockery::mock(EcoleService::class);
    $mock->shouldReceive('create')
        ->once()
        ->andReturn(new Ecole());

    $this->app->instance(EcoleService::class, $mock);

    // Test avec le mock
}
```

### Mocker une façade

```php
use Illuminate\Support\Facades\Log;

public function test_logging()
{
    Log::shouldReceive('info')
        ->once()
        ->with('École créée', Mockery::any());

    // Test
}
```

## 🔐 Tests d'Authentification

### Authentifier un utilisateur

```php
$user = User::factory()->create();

$response = $this->actingAs($user)
    ->getJson('/api/ecoles');
```

### Tester sans authentification

```php
$response = $this->getJson('/api/ecoles');
$response->assertUnauthorized();
```

### Tester avec un token Sanctum

```php
$user = User::factory()->create();
$token = $user->createToken('test')->plainTextToken;

$response = $this->withHeader('Authorization', 'Bearer ' . $token)
    ->getJson('/api/ecoles');
```

## 📝 Bonnes Pratiques

### 1. Nommer les tests clairement

```php
/** @test */
public function it_can_create_ecole_with_valid_data()
{
    // Test
}

/** @test */
public function it_fails_to_create_ecole_with_duplicate_code()
{
    // Test
}
```

### 2. Utiliser setUp() pour la préparation

```php
protected function setUp(): void
{
    parent::setUp();
    $this->user = User::factory()->create();
}
```

### 3. Tester les cas limites

```php
/** @test */
public function it_validates_required_fields()
{
    $response = $this->actingAs($this->user)
        ->postJson('/api/ecoles', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['code_ecole', 'libelle_ecole']);
}
```

### 4. Tester les permissions

```php
/** @test */
public function unauthorized_user_cannot_create_ecole()
{
    $response = $this->postJson('/api/ecoles', $data);
    $response->assertUnauthorized();
}
```

### 5. Tester les relations

```php
/** @test */
public function it_loads_departements_relation()
{
    $ecole = Ecole::factory()->create();
    $ecole->departements()->create([...]);

    $response = $this->actingAs($this->user)
        ->getJson("/api/ecoles/{$ecole->id}");

    $response->assertJsonStructure([
        'data' => [
            'departements' => [
                '*' => ['id', 'nom']
            ]
        ]
    ]);
}
```

## 🎯 Checklist de Test

Pour chaque module, tester :

- [ ] Liste (index) avec pagination
- [ ] Liste avec filtres
- [ ] Création avec données valides
- [ ] Création avec données invalides
- [ ] Validation des champs requis
- [ ] Validation des champs uniques
- [ ] Affichage d'un élément
- [ ] Affichage d'un élément inexistant
- [ ] Mise à jour avec données valides
- [ ] Mise à jour avec données invalides
- [ ] Suppression
- [ ] Suppression d'un élément inexistant
- [ ] Permissions (authentification)
- [ ] Relations (eager loading)
- [ ] Cas limites

## 📊 Coverage

### Générer un rapport de coverage

```bash
php artisan test --coverage
```

### Coverage HTML

```bash
php artisan test --coverage-html coverage
```

Ouvrir `coverage/index.html` dans un navigateur.

### Objectif de coverage

- **Minimum :** 70%
- **Recommandé :** 80%
- **Excellent :** 90%+

## 🐛 Debugging des Tests

### Afficher les erreurs

```bash
php artisan test -v
```

### Dump des données

```php
$response->dump();        // Afficher la réponse
$response->dumpHeaders(); // Afficher les headers
$response->dumpSession(); // Afficher la session
dd($response->json());    // Dump and die
```

### Logs pendant les tests

```php
Log::info('Debug info', ['data' => $data]);
```

## 🚀 CI/CD

### GitHub Actions

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test --coverage
```

## 📚 Ressources

- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Mockery Documentation](http://docs.mockery.io/)

---

**Dernière mise à jour :** Décembre 2024
