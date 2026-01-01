# Structure Candidat - Utilisateur avec PRU

## 🎯 Principe de Base

**Un Candidat EST un Utilisateur** avec des informations supplémentaires.

```
Utilisateur (table: utilisateurs)
    ↓ (relation 1:1)
Candidat (table: candidats)
    ↓ (relation 1:N)
Candidatures (table: candidatures)
```

## 📊 Structure des Tables

### Table `utilisateurs`
```sql
id (UUID, PK)
user_name              -- PRU (ex: POLY-2025-48392)
email
mot_de_passe          -- Hash du password
telephone
est_actif
email_verifie
type_utilisateur      -- 'CANDIDAT'
```

### Table `candidats`
```sql
utilisateur_id (UUID, PK, FK → utilisateurs.id)
nom_cand
prenom_cand
pru                   -- PRU (même valeur que utilisateurs.user_name)
date_naissance_cand
numero_cni
telephone_candidat
... (autres infos personnelles)
```

### Table `candidatures`
```sql
id (UUID, PK)
candidat_id (FK → candidats.utilisateur_id)
concours_id
session_id
statut_inscription    -- ACTIF | INVALIDE
date_candidature
date_inscription
```

## 🔑 PRU (Paiement Reference Unique)

Le **PRU** est l'identifiant unique du candidat :
- Généré lors du paiement
- Utilisé comme `username` dans la table `utilisateurs`
- Stocké aussi dans `candidats.pru` pour faciliter les requêtes
- Sert pour le login : `PRU + password`

### Format PRU
```
POLY-2025-48392
│    │    │
│    │    └─ Numéro unique
│    └────── Année
└─────────── Code concours
```

## 🔄 Workflow Création Candidat

### 1. Paiement (AVANT compte)
```sql
INSERT INTO paiements (
    concours_id,
    reference,          -- PRU
    montant,
    preuve_paiement,
    statut,
    candidat_id         -- NULL (pas encore de compte)
) VALUES (...);
```

### 2. Validation Paiement
```sql
UPDATE paiements 
SET statut = 'VERIFIED', 
    validated_at = NOW()
WHERE reference = 'POLY-2025-48392';
```

### 3. Création Compte (APRÈS validation)
```sql
-- 3.1 Créer utilisateur
INSERT INTO utilisateurs (
    id,
    user_name,          -- PRU
    email,
    mot_de_passe,       -- Hash
    telephone,
    type_utilisateur,
    est_actif
) VALUES (
    gen_random_uuid(),
    'POLY-2025-48392',  -- PRU comme username
    'jean@gmail.com',
    '$2y$10$...',
    '699000000',
    'CANDIDAT',
    true
);

-- 3.2 Créer candidat
INSERT INTO candidats (
    utilisateur_id,     -- UUID de l'utilisateur créé
    nom_cand,
    prenom_cand,
    pru,                -- PRU
    telephone_candidat
) VALUES (
    <utilisateur_id>,
    'Jean',
    'Ndzié',
    'POLY-2025-48392',
    '699000000'
);

-- 3.3 Lier paiement au candidat
UPDATE paiements 
SET candidat_id = <utilisateur_id>
WHERE reference = 'POLY-2025-48392';

-- 3.4 Créer candidature automatiquement
INSERT INTO candidatures (
    candidat_id,
    concours_id,
    session_id,
    statut_inscription,
    date_candidature,
    date_inscription
) VALUES (
    <utilisateur_id>,
    <concours_id>,
    <session_id>,
    'ACTIF',
    NOW(),
    <date_validation_paiement>
);
```

## 🔐 Authentification

### Login avec PRU + Password
```php
// Request
POST /api/candidates/login
{
    "pru": "POLY-2025-48392",
    "password": "MotDePasseFort123!"
}

// Backend
$utilisateur = Utilisateur::where('user_name', $pru)
    ->where('type_utilisateur', 'CANDIDAT')
    ->where('est_actif', true)
    ->first();

if ($utilisateur && Hash::check($password, $utilisateur->mot_de_passe)) {
    $token = $utilisateur->createToken('auth_token')->plainTextToken;
    return response()->json(['token' => $token]);
}
```

## 📝 Relations Eloquent

### Utilisateur
```php
public function candidat()
{
    return $this->hasOne(Candidat::class, 'utilisateur_id');
}
```

### Candidat
```php
public function utilisateur()
{
    return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
}

public function candidatures()
{
    return $this->hasMany(Candidature::class, 'candidat_id', 'utilisateur_id');
}

public function paiements()
{
    return $this->hasMany(Paiement::class, 'candidat_id', 'utilisateur_id');
}
```

### Candidature
```php
public function candidat()
{
    return $this->belongsTo(Candidat::class, 'candidat_id', 'utilisateur_id');
}
```

## ✅ Points Clés

1. **PRU = Username** : Le PRU est stocké dans `utilisateurs.user_name`
2. **Candidat.pru** : Dupliqué pour faciliter les requêtes directes
3. **utilisateur_id** : Clé primaire de `candidats`, foreign key vers `utilisateurs.id`
4. **candidat_id** : Dans `candidatures`, référence `candidats.utilisateur_id`
5. **Type utilisateur** : Toujours `'CANDIDAT'` pour les candidats
6. **Login** : Utilise `user_name` (PRU) + `mot_de_passe`

## 🚫 Ce qui N'existe PAS

- ❌ Création candidat sans paiement validé
- ❌ Username différent du PRU
- ❌ Candidat sans utilisateur
- ❌ Login avec email (uniquement PRU)
- ❌ Modification du PRU après création
