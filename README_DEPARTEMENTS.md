# Module Départements - API EnrolCM

## 📋 Vue d'ensemble

Module complet de gestion des départements académiques avec architecture Laravel standard.

## 🏗️ Architecture

### Structure des fichiers
```
app/
├── DTOs/Departements/
│   └── CreateDepartementDTO.php
├── Exceptions/Business/
│   └── DepartementException.php
├── Http/
│   ├── Controllers/
│   │   └── DepartementController.php
│   ├── Requests/Departements/
│   │   ├── StoreDepartementRequest.php
│   │   └── UpdateDepartementRequest.php
│   └── Resources/
│       └── DepartementResource.php
├── Models/
│   └── Departement.php
└── Services/Departements/
    └── DepartementService.php

routes/api/
└── departements.php
```

## 🔗 Relations

- **Departement → Ecole** : `belongsTo` (un département appartient à une école)
- **Departement → Filiere** : `hasMany` (un département a plusieurs filières)

## 📊 Structure de la table `departements`

| Champ | Type | Description |
|-------|------|-------------|
| id | uuid | Identifiant unique |
| code_departement | string(10) | Code unique du département |
| libelle_departement | string(200) | Nom du département |
| ecole_id | uuid | Référence à l'école |
| desc_departement | text | Description |
| est_actif | boolean | Statut actif/inactif |

## 🚀 Endpoints API

### Base URL: `/api/v1/departements`

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/` | Liste paginée avec filtres |
| POST | `/` | Créer un département |
| GET | `/{id}` | Afficher par ID |
| GET | `/code/{code}` | Afficher par code |
| PUT | `/{id}` | Mettre à jour |
| DELETE | `/{id}` | Supprimer |
| PATCH | `/{id}/toggle-status` | Activer/désactiver |
| GET | `/actifs` | Liste des départements actifs |

## 📝 Exemples de requêtes

### 1. Créer un département
```json
POST /api/v1/departements
{
  "code_departement": "INFO",
  "libelle_departement": "Département Informatique",
  "ecole_id": "uuid-de-l-ecole",
  "desc_departement": "Département spécialisé en informatique",
  "est_actif": true
}
```

### 2. Liste avec filtres
```
GET /api/v1/departements?est_actif=1&ecole_id=uuid&search=Info&per_page=15
```

### 3. Mettre à jour
```json
PUT /api/v1/departements/{id}
{
  "code_departement": "INFO",
  "libelle_departement": "Département Informatique et Réseaux",
  "ecole_id": "uuid-de-l-ecole",
  "desc_departement": "Département spécialisé en informatique et réseaux",
  "est_actif": true
}
```

## ✅ Validations

### Champs obligatoires
- `code_departement` : max 10 caractères, unique
- `libelle_departement` : max 200 caractères

### Champs optionnels
- `ecole_id` : doit exister dans la table ecoles
- `desc_departement` : texte libre
- `est_actif` : boolean (défaut: true)

### Messages d'erreur
- "Le code du département est obligatoire"
- "Ce code département existe déjà"
- "L'école sélectionnée n'existe pas"
- "Impossible de supprimer : des filières sont associées à ce département"

## 🔒 Réponses API

### Succès (200/201)
```json
{
  "success": true,
  "message": "Département créé avec succès",
  "data": {
    "id": "uuid",
    "code_departement": "INFO",
    "libelle_departement": "Département Informatique",
    "ecole_id": "uuid",
    "desc_departement": "...",
    "est_actif": true,
    "created_at": "2024-01-01 10:00:00",
    "ecole": {...},
    "filieres": [...]
  }
}
```

### Erreur de validation (422)
```json
{
  "success": false,
  "message": "Erreur de validation",
  "errors": {
    "code_departement": ["Ce code département existe déjà"]
  }
}
```

### Erreur (404/500)
```json
{
  "success": false,
  "message": "Département non trouvé"
}
```

## 🎯 Fonctionnalités

✅ CRUD complet
✅ Validation stricte
✅ Gestion des erreurs
✅ Logs détaillés
✅ Pagination
✅ Filtres (statut, école, recherche)
✅ Soft delete via `est_actif`
✅ Relations Eloquent
✅ Réponses JSON standardisées
✅ DTOs pour la validation
✅ Service Layer
✅ Exceptions métier
✅ Protection contre suppression si filières associées

## 🚦 Statuts HTTP

- `200` : Succès
- `201` : Créé
- `404` : Non trouvé
- `422` : Erreur de validation
- `500` : Erreur serveur
