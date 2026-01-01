# Services TODO - Méthodes à ajouter

Ce document liste les méthodes qui doivent être ajoutées dans les services pour compléter l'architecture propre.

## UserService (branche: main)

**Fichier:** `app/Services/Users/UserService.php`

### Méthodes à ajouter:

```php
/**
 * Créer un utilisateur candidat avec PRU comme username
 */
public function createCandidatUser(string $pru, string $email, string $password, string $telephone): Utilisateur
{
    return Utilisateur::create([
        'user_name' => $pru,
        'email' => $email,
        'mot_de_passe' => Hash::make($password),
        'telephone' => $telephone,
        'type_utilisateur' => TypeUtilisateur::CANDIDAT,
        'est_actif' => true,
        'email_verifie' => false,
    ]);
}

/**
 * Vérifier si un email existe déjà
 */
public function emailExists(string $email): bool
{
    return Utilisateur::where('email', $email)->exists();
}

/**
 * Authentifier un candidat avec PRU + password
 */
public function authenticateCandidat(string $pru, string $password): ?Utilisateur
{
    $utilisateur = Utilisateur::where('user_name', $pru)
        ->where('type_utilisateur', TypeUtilisateur::CANDIDAT)
        ->where('est_actif', true)
        ->first();

    if (!$utilisateur || !Hash::check($password, $utilisateur->mot_de_passe)) {
        return null;
    }

    return $utilisateur;
}

/**
 * Générer un token d'authentification
 */
public function generateToken(Utilisateur $utilisateur): string
{
    return $utilisateur->createToken('auth_token')->plainTextToken;
}

/**
 * Désactiver un utilisateur
 */
public function deactivate(string $utilisateurId): bool
{
    return DB::transaction(function () use ($utilisateurId) {
        $utilisateur = Utilisateur::findOrFail($utilisateurId);
        $utilisateur->update(['est_actif' => false]);
        $utilisateur->tokens()->delete();
        return true;
    });
}

/**
 * Activer un utilisateur
 */
public function activate(string $utilisateurId): bool
{
    $utilisateur = Utilisateur::findOrFail($utilisateurId);
    $utilisateur->update(['est_actif' => true]);
    return true;
}
```

**Imports nécessaires:**
```php
use App\Models\Utilisateur;
use App\Enums\TypeUtilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
```

---

## PaiementService (branche: feature/payment-receipt-ocr)

**Fichier:** `app/Services/Payment/PaiementService.php`

### Méthodes déjà présentes ✅:

- `isPRUValid(string $pru, string $concoursId): array` ✅
- `linkToCandidat(string $pru, string $concoursId, string $candidatId): Paiement` ✅
- `getValidationDate(string $pru, string $concoursId): ?\DateTime` ✅

**Aucune modification nécessaire** - Les méthodes sont déjà implémentées correctement.

---

## Architecture finale

### CandidatService (feature/candidats)
- Ne connaît que: `Candidat`, `Candidature`
- Délègue à `UserService` pour: création utilisateur, authentification, tokens, activation/désactivation
- Délègue à `PaiementService` pour: vérification PRU, liaison paiement, date validation

### UserService (main)
- Gère uniquement: `Utilisateur`
- Responsabilités: CRUD utilisateurs, authentification, tokens

### PaiementService (feature/payment-receipt-ocr)
- Gère uniquement: `Paiement`, `ConcoursPaiement`
- Responsabilités: Upload preuve, OCR, validation, PRU

---

## Ordre de merge recommandé

1. **main** ← Ajouter méthodes dans `UserService`
2. **main** ← merge `feature/concours` (gestion concours)
3. **main** ← merge `feature/payment-receipt-ocr` (système paiement)
4. **main** ← merge `feature/candidats` (inscription candidats)

Cet ordre garantit que toutes les dépendances sont disponibles avant le merge final.
