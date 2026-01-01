# Tests Workflow Concours

## Vue d'ensemble

Suite de tests complète (10 tests, 42 assertions) validant l'API Concours harmonisée et ses 4 workflows.

## Tests de Création

### 1. `it_creates_concours_template_minimal`
**Objectif** : Valider création de concours template sans contraintes
```php
// Teste création minimale
$data = ['libelle_concours' => 'Template Test', 'description' => '...'];
$concours = $service->create(CreateConcoursDTO::fromRequest($data));

// Assertions
- Instance Concours créée
- Libellé correct
- Description sauvegardée
- spec_concours_id = null
- date_examen = null
- 0 sessions attachées
```

### 2. `it_creates_concours_with_specs_only`
**Objectif** : Valider création avec spécifications uniquement
```php
// Teste specs sans session
$spec = SpecConcours::create([...]);
$data = ['libelle_concours' => '...', 'spec_concours_id' => $spec->id];
$concours = $service->create(CreateConcoursDTO::fromRequest($data));

// Assertions
- Specs correctement liées
- 0 sessions (pas de session fournie)
```

### 3. `it_creates_concours_with_session_and_dates`
**Objectif** : Valider création avec session et validation dates
```php
// Teste session avec dates
$session = Session::create([...]);
$data = [
    'libelle_concours' => '...',
    'session_id' => $session->id,
    'date_debut' => '2025-06-15',
    'date_limite_depot' => '2025-05-15',
    'nombre_places' => 300
];

// Assertions
- 1 session attachée
- Dates cohérentes sauvegardées
- Places définies
```

### 4. `it_creates_concours_complet_specs_session_dates`
**Objectif** : Valider création complète avec tous les champs
```php
// Teste workflow complet
$data = [
    'libelle_concours' => '...',
    'spec_concours_id' => $spec->id,
    'session_id' => $session->id,
    'date_debut' => '2025-06-15',
    'date_limite_depot' => '2025-05-15',
    'nombre_places' => 500,
    'description' => '...'
];

// Assertions
- Specs ET session liées
- Toutes les données sauvegardées
- Relations correctes
```

## Tests de Mise à Jour

### 5. `it_updates_concours_to_add_session`
**Objectif** : Valider attachement de session à template
```php
// Template initial
$template = Concours::create(['libelle_concours' => 'Template']);

// Update avec session
$updateData = [
    'session_id' => $session->id,
    'date_debut' => '2025-06-15',
    'nombre_places' => 400
];
$updated = $service->update($template->id, UpdateConcoursDTO::fromRequest($updateData));

// Assertions
- Session attachée (0 → 1)
- Dates ajoutées
- Places définies
```

### 6. `it_updates_concours_to_change_session`
**Objectif** : Valider changement de session
```php
// Concours avec session1
$concours = $service->create([...session1...]);

// Changement vers session2
$updated = $service->update($concours->id,
    UpdateConcoursDTO::fromRequest(['session_id' => $session2->id, ...])
);

// Assertions
- Nouvelle session attachée
- Ancienne session détachée
- Données mises à jour
```

## Tests de Validation

### 7. `it_validates_dates_when_session_provided`
**Objectif** : Valider rejet dates incohérentes
```php
// Teste dates invalides
$this->expectException(\Exception::class);
$service->create(CreateConcoursDTO::fromRequest([
    'libelle_concours' => '...',
    'session_id' => $session->id,
    'date_debut' => '2025-06-15',
    'date_limite_depot' => '2025-06-20' // APRÈS date examen = invalide
]));
```

### 8. `it_validates_unique_libelle_per_session`
**Objectif** : Valider unicité par session
```php
// Création premier concours
$service->create([...session1, 'libelle' => 'Unique'...]);

// Tentative doublon = erreur
$this->expectException(\Exception::class);
$service->create([...session1, 'libelle' => 'Unique'...]); // Même session
```

### 9. `it_allows_same_libelle_for_different_sessions`
**Objectif** : Valider même nom autorisé pour sessions différentes
```php
// Même libellé, sessions différentes = OK
$concours1 = $service->create([...session1, 'libelle' => 'Même Nom'...]);
$concours2 = $service->create([...session2, 'libelle' => 'Même Nom'...]);

// Assertions
- 2 concours créés
- IDs différents
- Sessions différentes
```

### 10. `it_prevents_creation_with_inactive_session`
**Objectif** : Valider rejet sessions inactives
```php
$inactiveSession = Session::create(['est_actif' => false]);

$this->expectException(\Exception::class);
$service->create(CreateConcoursDTO::fromRequest([
    'libelle_concours' => '...',
    'session_id' => $inactiveSession->id
]));
```

## Métriques

- **Tests** : 10
- **Assertions** : 42
- **Taux de succès** : 100%
- **Durée** : ~7 secondes
- **Lignes de code** : 343

## Configuration

```php
use RefreshDatabase, WithFaker;

protected function setUp(): void
{
    parent::setUp();
    $this->concoursService = app(ConcoursService::class);
}
```

## Couverture Fonctionnelle

✅ **Créations** : Templates, specs seules, sessions seules, complets
✅ **Updates** : Attachement session, changement session
✅ **Validations** : Dates, unicité, sessions actives
✅ **Erreurs** : Gestion exceptions appropriée
✅ **Relations** : Sessions, specs correctement gérées

## Maintenance

Les tests utilisent `RefreshDatabase` pour isolation complète entre tests. Données de test générées automatiquement avec modèles et relations.
