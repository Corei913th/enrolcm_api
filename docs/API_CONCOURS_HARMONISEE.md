# API Concours Harmonisée

## Vue d'ensemble

L'API Concours a été refactorisée pour offrir une approche flexible et harmonisée entre les opérations CREATE et UPDATE, permettant 4 workflows différents.

## Workflows Disponibles

### 1. Concours Template
**Usage** : Créer un concours réutilisable sans contraintes temporelles
```json
POST /api/concours
{
  "libelle_concours": "Polytechnique",
  "description": "Concours général réutilisable"
}
```

### 2. Concours avec Spécifications
**Usage** : Créer un concours avec règles métier définies
```json
POST /api/concours
{
  "libelle_concours": "Polytechnique Spécialisé",
  "spec_concours_id": "uuid-specs",
  "description": "Avec spécifications détaillées"
}
```

### 3. Concours avec Session
**Usage** : Créer un concours directement lié à une session
```json
POST /api/concours
{
  "libelle_concours": "Polytechnique 2025-2026",
  "session_id": "uuid-session",
  "date_debut": "2025-06-15",
  "date_limite_depot": "2025-05-15",
  "nombre_places": 500
}
```

### 4. Concours Complet
**Usage** : Créer un concours avec tous les paramètres
```json
POST /api/concours
{
  "libelle_concours": "Polytechnique Complet",
  "spec_concours_id": "uuid-specs",
  "session_id": "uuid-session",
  "date_debut": "2025-06-15",
  "date_limite_depot": "2025-05-15",
  "nombre_places": 500,
  "description": "Tous les champs définis"
}
```

## Mise à Jour Flexible

### Attacher une Session
```json
PUT /api/concours/{id}
{
  "session_id": "uuid-session",
  "date_debut": "2025-06-15",
  "date_limite_depot": "2025-05-15",
  "nombre_places": 450
}
```

### Changer de Session
```json
PUT /api/concours/{id}
{
  "session_id": "nouvelle-uuid-session",
  "date_debut": "2026-06-15",
  "nombre_places": 550
}
```

## Règles de Validation

### Champs Obligatoires
- `libelle_concours` : Toujours requis

### Champs Optionnels avec `sometimes`
- `spec_concours_id` : Validation UUID + existence si fourni
- `session_id` : Validation UUID + existence si fourni
- `est_actif` : Valeur par défaut `true`

### Champs Conditionnels
- `date_debut`, `date_limite_depot`, `nombre_places` : Validés seulement si `session_id` fourni
- `date_limite_depot < date_debut` : Règle métier pour cohérence dates

### Contraintes Métier
- **Unicité** : Même libellé impossible pour même session
- **Sessions actives** : Impossible avec sessions inactives
- **Dates cohérentes** : Limite dépôt avant date examen

## Cohérence API

### CreateConcoursRequest = UpdateConcoursRequest
- Même structure de validation
- Même messages d'erreur
- Même logique métier

### DTOs Harmonisés
- `CreateConcoursDTO` et `UpdateConcoursDTO` compatibles
- Mapping DB cohérent (`date_debut` → `date_examen`)
- Gestion des champs optionnels uniforme

## Avantages

✅ **Flexibilité maximale** : 4 workflows pour différents cas d'usage
✅ **API cohérente** : CREATE et UPDATE identiques
✅ **Évolutivité** : Ajout de sessions simplifié
✅ **Maintenabilité** : Code DRY et validations centralisées
✅ **Robustesse** : Validations complètes et tests exhaustifs
