# Middleware de Gestion des Rôles et Permissions

## Middlewares disponibles

### 1. `role` - Vérification des rôles

Vérifie si l'utilisateur connecté possède au moins un des rôles spécifiés.

**Utilisation :**
```php
// Un seul rôle requis
Route::middleware(['auth:sanctum', 'role:ADMIN'])->group(function () {
    // Routes réservées aux admins
});

// Plusieurs rôles possibles (OU logique)
Route::middleware(['auth:sanctum', 'role:ADMIN,RESPONSABLE_CENTRE'])->group(function () {
    // Routes accessibles par ADMIN OU RESPONSABLE_CENTRE
});
```

**Rôles disponibles :**
- `ADMIN`
- `CANDIDAT`
- `CORRECTEUR`
- `RESPONSABLE_CENTRE`

### 2. `permission` - Vérification des permissions

Vérifie si l'utilisateur connecté possède au moins une des permissions spécifiées (via ses rôles).

**Utilisation :**
```php
// Une seule permission requise
Route::middleware(['auth:sanctum', 'permission:VALIDER_CANDIDATURE'])->group(function () {
    // Routes nécessitant la permission VALIDER_CANDIDATURE
});

// Plusieurs permissions possibles (OU logique)
Route::middleware(['auth:sanctum', 'permission:VALIDER_CANDIDATURE,REJETER_CANDIDATURE'])->group(function () {
    // Routes nécessitant VALIDER_CANDIDATURE OU REJETER_CANDIDATURE
});
```

## Exemples d'utilisation

### Protéger une route simple
```php
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware(['auth:sanctum', 'role:ADMIN']);
```

### Protéger un groupe de routes
```php
Route::middleware(['auth:sanctum', 'role:ADMIN'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
```

### Combiner rôle et permission
```php
Route::middleware(['auth:sanctum', 'role:ADMIN', 'permission:MANAGE_USERS'])->group(function () {
    // Routes nécessitant le rôle ADMIN ET la permission MANAGE_USERS
});
```

### Plusieurs rôles autorisés
```php
// ADMIN ou RESPONSABLE_CENTRE peuvent accéder
Route::middleware(['auth:sanctum', 'role:ADMIN,RESPONSABLE_CENTRE'])->group(function () {
    Route::get('/candidatures/pending', [CandidatureController::class, 'pending']);
});
```

## Réponses d'erreur

### Non authentifié (401)
```json
{
    "success": false,
    "message": "Non authentifié",
    "error_code": "UNAUTHENTICATED"
}
```

### Accès refusé - Rôle manquant (403)
```json
{
    "success": false,
    "message": "Accès refusé. Rôle requis: ADMIN ou RESPONSABLE_CENTRE",
    "error_code": "FORBIDDEN"
}
```

### Accès refusé - Permission manquante (403)
```json
{
    "success": false,
    "message": "Accès refusé. Permission requise: VALIDER_CANDIDATURE ou REJETER_CANDIDATURE",
    "error_code": "FORBIDDEN"
}
```

## Utilisation dans les controllers

Vous pouvez aussi vérifier les rôles/permissions directement dans les controllers :

```php
use App\Services\Roles\RoleService;

class CandidatureController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService
    ) {}

    public function validate(Request $request, string $id)
    {
        $user = $request->user();
        
        // Vérifier le rôle
        if (!$this->roleService->hasRole($user, 'ADMIN')) {
            return api_error('Accès refusé', null, 403);
        }
        
        // Vérifier la permission
        if (!$this->roleService->hasPermission($user, 'VALIDER_CANDIDATURE')) {
            return api_error('Permission insuffisante', null, 403);
        }
        
        // Logique de validation...
    }
}
```

## Bonnes pratiques

1. **Toujours utiliser `auth:sanctum` avant `role` ou `permission`**
   ```php
   // ✅ Correct
   ->middleware(['auth:sanctum', 'role:ADMIN'])
   
   // ❌ Incorrect
   ->middleware(['role:ADMIN', 'auth:sanctum'])
   ```

2. **Préférer les middlewares aux vérifications manuelles**
   - Plus propre et maintenable
   - Centralise la logique d'autorisation

3. **Utiliser des groupes pour éviter la répétition**
   ```php
   Route::middleware(['auth:sanctum', 'role:ADMIN'])->prefix('admin')->group(function () {
       // Toutes les routes ici sont protégées
   });
   ```

4. **Documenter les rôles requis dans les commentaires**
   ```php
   /**
    * Valider une candidature
    * 
    * @requires role:ADMIN,RESPONSABLE_CENTRE
    * @requires permission:VALIDER_CANDIDATURE
    */
   public function validate(Request $request, string $id) { }
   ```
