# Guide de Tests - Fonctionnalité OCR Paiement

## Installation des dépendances de test

```bash
composer install --dev
```

## Exécuter tous les tests

```bash
php artisan test
```

## Exécuter les tests par catégorie

### Tests unitaires uniquement
```bash
php artisan test --testsuite=Unit
```

### Tests de fonctionnalité uniquement
```bash
php artisan test --testsuite=Feature
```

### Tests spécifiques à l'OCR
```bash
php artisan test --filter=PaymentReceipt
php artisan test --filter=TesseractOcr
php artisan test --filter=PaymentVerification
```

## Exécuter les tests avec couverture

```bash
php artisan test --coverage
```

## Structure des tests

### Tests Unitaires (`tests/Unit/`)

**TesseractOcrServiceTest.php**
- ✓ Parsing des montants avec espaces
- ✓ Parsing des dates dans différents formats
- ✓ Gestion des valeurs invalides
- ✓ Calcul du score de confiance
- ✓ Extraction de patterns

**PaymentVerificationServiceTest.php**
- ✓ Validation du type de fichier
- ✓ Validation de la taille du fichier
- ✓ Vérification du statut de paiement
- ✓ Récupération du reçu vérifié

### Tests de Fonctionnalité (`tests/Feature/`)

**PaymentReceiptTest.php**
- ✓ Upload de reçu par candidat
- ✓ Validation du format de fichier
- ✓ Consultation du reçu par candidat
- ✓ Liste des reçus en attente (admin)
- ✓ Vérification de reçu (admin)
- ✓ Rejet de reçu avec motif (admin)
- ✓ Protection des endpoints (authentification)

## Seeders de test

### Peupler la base avec des données de test

```bash
php artisan db:seed --class=PaymentReceiptSeeder
```

Cela créera :
- 1 reçu vérifié
- 2 reçus en attente
- 1 reçu rejeté
- 1 reçu avec données variées

### Réinitialiser et peupler

```bash
php artisan migrate:fresh --seed
```

## Factories disponibles

### PaymentReceiptFactory

```php
// Créer un reçu en attente
PaymentReceipt::factory()->create();

// Créer un reçu vérifié
PaymentReceipt::factory()->verifie()->create();

// Créer un reçu rejeté
PaymentReceipt::factory()->rejete()->create();

// Créer plusieurs reçus
PaymentReceipt::factory()->count(10)->create();
```

### CandidatFactory

```php
// Créer un candidat
Candidat::factory()->create();

// Créer un candidat avec reçu vérifié
$candidat = Candidat::factory()->create();
PaymentReceipt::factory()->verifie()->create([
    'candidat_id' => $candidat->utilisateur_id
]);
```

## Tests manuels avec Postman/Insomnia

### 1. Upload de reçu (Candidat)

```http
POST /api/payment/receipts/upload
Authorization: Bearer {token}
Content-Type: multipart/form-data

receipt_image: [fichier image]
```

### 2. Consulter son reçu (Candidat)

```http
GET /api/payment/receipts/my-receipt
Authorization: Bearer {token}
```

### 3. Liste des reçus en attente (Admin)

```http
GET /api/payment/admin/receipts/pending
Authorization: Bearer {admin_token}
```

### 4. Vérifier un reçu (Admin)

```http
POST /api/payment/admin/receipts/{id}/verify
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "statut": "verifie"
}
```

### 5. Rejeter un reçu (Admin)

```http
POST /api/payment/admin/receipts/{id}/verify
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "statut": "rejete",
  "motif_rejet": "Montant incorrect"
}
```

## Debugging

### Activer les logs détaillés

Dans `.env` :
```env
LOG_LEVEL=debug
APP_DEBUG=true
```

### Voir les requêtes SQL

```php
DB::enableQueryLog();
// ... votre code
dd(DB::getQueryLog());
```

## Bonnes pratiques

1. **Toujours exécuter les tests avant de commit**
   ```bash
   php artisan test
   ```

2. **Utiliser les factories pour les données de test**
   - Ne pas créer manuellement les données
   - Utiliser les factories pour la cohérence

3. **Nettoyer après les tests**
   - Les tests utilisent `RefreshDatabase`
   - La base de test est réinitialisée automatiquement

4. **Tester les cas limites**
   - Fichiers invalides
   - Montants incorrects
   - Dates invalides
   - Authentification manquante

## Résolution de problèmes

### Erreur "Class not found"
```bash
composer dump-autoload
```

### Erreur de base de données
```bash
php artisan migrate:fresh --env=testing
```

### Tesseract non trouvé
Vérifier l'installation :
```bash
tesseract --version
```

Voir `docs/TESSERACT_INSTALLATION.md` pour l'installation.
