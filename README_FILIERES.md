# Module Filières - API EnrolCM

## 📋 Vue d'ensemble

Module complet de gestion des filières académiques avec architecture Laravel standard.

## 🏗️ Architecture

### Structure des fichiers
```
app/
├── DTOs/Filieres/
│   └── CreateFiliereDTO.php
├── Exceptions/Business/
│   └── FiliereException.php
├── Http/
│   ├── Controllers/
│   │   └── FiliereController.php
│   ├── Requests/Filieres/
│   │   ├── StoreFiliereRequest.php
│   │   └── UpdateFiliereRequest.php
│   └── Resources/
│       └── FiliereResource.php
├── Models/
│   └── Filiere.php
└── Services/Filieres/
    └── FiliereService.php

routes/api/
└── filieres.php
```

## 🔗 Relations

- **Filiere → Departement** : `belongsTo` (une filière appartient à un département)
- **Filiere → Niveau** : `hasMany` (une filière a plusieurs niveaux)

## 📊 Structure de la table `filieres`

| Champ | Type | Description |
|-------|------|-------------|
| id | uuid | Identifiant unique |
| code_filiere | string(10) | Code unique de la filière |
| libelle_filiere | string(200) | Nom de la filière |
| departement_id | uuid | Référence au département |
| desc_filiere | text | Description |
| est_actif | boolean | Statut actif/inactif |

## 🚀 Endpoints API

### Base URL: `/api/v1/filieres`

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/` | Liste paginée avec filtres |
| POST | `/` | Créer une filière |
| GET | `/{id}` | Afficher par ID |
| GET | `/code/{code}` | Afficher par code |
| PUT | `/{id}` | Mettre à jour |
| DELETE | `/{id}` | Supprimer |
| PATCH | `/{id}/toggle-status` | Activer/désactiver |
| GET | `/actives` | Liste des filières actives |

## 📝 Exemples de requêtes

### 1. Créer une filière
```json
POST /api/v1/filieres
{
  "code_filiere": "GL",
  "libelle_filiere": "Génie Logiciel",
  "departement_id": "uuid-du-departement",
  "desc_filiere": "Filière spécialisée en développement logiciel",
  "est_actif": true
}
```

### 2. Liste avec filtres
```
GET /api/v1/filieres?est_actif=1&departement_id=uuid&search=Génie&per_page=15
```

### 3. Mettre à jour
```json
PUT /api/v1/filieres/{id}
{
  "code_filiere": "GL",
  "libelle_filiere": "Génie Logiciel et Systèmes",
  "departement_id": "uuid-du-departement",
  "desc_filiere": "Filière spécialisée en développement logiciel et systèmes",
  "est_actif": true
}
```

## ✅ Validations

### Champs obligatoires
- `code_filiere` : max 10 caractères, unique
- `libelle_filiere` : max 200 caractères

### Champs optionnels
- `departement_id` : doit exister dans la table departements
- `desc_filiere` : texte libre
- `est_actif` : boolean (défaut: true)

### Messages d'erreur
- "Le code de la filière est obligatoire"
- "Cette filière existe déjà"
- "Le département sélectionné est invalide"
- "Impossible de supprimer : des niveaux sont associés à cette filière"

## 🔒 Réponses API

### Succès (200/201)
```json
{
  "success": true,
  "message": "Filière créée avec succès",
  "data": {
    "id": "uuid",
    "code_filiere": "GL",
    "libelle_filiere": "Génie Logiciel",
    "departement_id": "uuid",
    "desc_filiere": "...",
    "est_actif": true,
    "created_at": "2024-01-01 10:00:00",
    "departement": {...},
    "niveaux": [...]
  }
}
```

### Erreur de validation (422)
```json
{
  "success": false,
  "message": "Erreur de validation",
  "errors": {
    "code_filiere": ["Cette filière existe déjà"]
  }
}
```

### Erreur (404/500)
```json
{
  "success": false,
  "message": "Filière non trouvée"
}
```

## 🎯 Fonctionnalités

✅ CRUD complet
✅ Validation stricte
✅ Gestion des erreurs
✅ Logs détaillés
✅ Pagination
✅ Filtres (statut, département, recherche)
✅ Soft delete via `est_actif`
✅ Relations Eloquent
✅ Réponses JSON standardisées
✅ DTOs pour la validation
✅ Service Layer
✅ Exceptions métier
✅ Protection contre suppression si niveaux associés

## 🚦 Statuts HTTP

- `200` : Succès
- `201` : Créé
- `404` : Non trouvé
- `422` : Erreur de validation
- `500` : Erreur serveur
