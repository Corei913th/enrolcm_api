# Workflow Candidature - Cohérence avec le Réel

## 🎯 Workflow Complet

### Étape 1 : Paiement (AVANT compte)
```
1. Candidat consulte concours disponibles
   GET /api/concours/ouverts

2. Candidat consulte infos paiement
   GET /api/concours/{id}/payment-info
   → Reçoit: montant, banque, compte, PRU format

3. Candidat paie à la banque (externe)
   → Obtient PRU (ex: POLY-2025-48392)

4. Candidat upload preuve
   POST /api/payments
   Body: { concours_id, reference (PRU), montant, preuve }
   → OCR extrait données
   → Statut: PENDING
   → candidat_id = NULL (pas encore de compte)

5. Auto-validation
   → Vérifie: référence = PRU, montant ±5%, date ≤ limite
   → Si OK: statut = VERIFIED
   → Sinon: reste PENDING (agent valide manuellement)
```

### Étape 2 : Création Compte (APRÈS paiement validé)
```
1. Candidat vérifie son PRU
   POST /api/candidates/verify-pru
   Body: { pru, concours_id }
   → Vérifie: paiement VERIFIED + candidat_id NULL + date valide
   → Retourne: { valid: true/false }

2. Candidat crée son compte
   POST /api/candidates/register
   Body: { pru, nom, prenom, email, telephone, password, concours_id, session_id }
   → Crée candidat
   → Lie paiement au candidat (UPDATE paiements SET candidat_id = ...)
   → Crée candidature automatiquement avec statut ACTIF
```

### Étape 3 : Candidature Créée Automatiquement
```sql
INSERT INTO candidatures (
    candidat_id,
    concours_id,
    session_id,
    statut_inscription,
    date_candidature,
    date_inscription
) VALUES (
    <nouveau_candidat_id>,
    <concours_id>,
    <session_id>,
    'ACTIF',
    NOW(),
    <date_validation_paiement>
);
```

## 📊 Structure Candidature

### Champs Essentiels
```php
candidat_id          // UUID du candidat (créé APRÈS paiement)
concours_id          // UUID du concours
session_id           // UUID de la session
statut_inscription   // ACTIF | INVALIDE
date_candidature     // Date de création candidature
date_inscription     // Date de validation paiement
```

### Statuts
- **ACTIF** : Paiement validé, candidature active
- **INVALIDE** : Paiement rejeté ou problème détecté

### Relations
```php
candidat()           // belongsTo Candidat (via candidat_id)
concours()           // belongsTo Concours
session()            // belongsTo Session
paiement()           // hasOne Paiement (via candidat_id + concours_id)
```

## ✅ Méthodes Cohérentes

```php
// Vérifications statut
isActif(): bool              // statut === ACTIF
isInvalide(): bool           // statut === INVALIDE

// Actions
activer(): void              // Passe à ACTIF
invalider(): void            // Passe à INVALIDE

// Vérifications
hasPaiementValide(): bool    // Paiement existe et VERIFIED
```

## 🔄 Cycle de Vie

```
1. Paiement PENDING (candidat_id = NULL)
   ↓
2. Paiement VERIFIED (candidat_id = NULL)
   ↓
3. Création compte candidat
   ↓
4. Liaison paiement → candidat (UPDATE paiements)
   ↓
5. Création candidature ACTIF (automatique)
   ↓
6. Candidat peut se connecter et compléter dossier
```

## ⚠️ Points Importants

1. **Candidature créée APRÈS paiement validé** : Pas de candidature sans paiement
2. **candidat_id nullable dans paiements** : Permet paiement avant compte
3. **Statut ACTIF par défaut** : Si paiement VERIFIED
4. **Pas de statut BROUILLON/SUSPENDUE** : Simplifié à ACTIF/INVALIDE
5. **Une candidature = un concours + une session** : Via table pivot concours_session

## 🚫 Ce qui N'existe PAS

- ❌ Candidature sans paiement validé
- ❌ Statuts BROUILLON, SUSPENDUE, CONFIRMEE
- ❌ Création manuelle de candidature
- ❌ Paiement après création compte
- ❌ Modification du PRU après création
