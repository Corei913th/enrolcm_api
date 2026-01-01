# Module Matières - API EnrolCM

## 📋 Vue d'ensemble

Module complet de gestion des matières académiques avec architecture Laravel standard.

## 🏗️ Architecture

### Structure des fichiers
```
app/
├── DTOs/Matieres/
│   └── CreateMatiereDTO.php
├── Exceptions/Business/
│   └── MatiereException.php
├── Http/
│   ├── Controllers/
│   │   └── MatiereController.php
│   ├── Requests/Matieres/
│   │   ├── StoreMatiereRequest.php
│   │   └── UpdateMatiereRequest.php
│   └── Resources/
│       └── MatiereResource.php
├── Models/
│   └── Matiere.php
└── Services/Matieres/
    └── MatiereService.php

routes/api/
└── matieres.php
```

## 🔗 Relations

- **Matiere → Niveau** : `belongsToMany` (relation many-to-many via `niveau_matiere`)

## 📊 Structure de la table `matieres`

| Champ | Type | Description |
|-------|------|-------------|
| id | uuid | Identifiant unique |
| code_matiere | string(10) | Code unique de la matière |
| libelle_matiere | string(200) | Nom de la matière |
| coefficient | integer | Coefficient (1-10) |
| est_actif | boolean | Statut actif/inactif |

## 🚀 Endpoints API

### Base URL: `/api/v1/matieres`

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/` | Liste paginée avec filtres |
| POST | `/` | Créer une matière |
| GET | `/{id}` | Afficher par ID |
| GET | `/code/{code}` | Afficher par code |
| PUT | `/{id}` | Mettre à jour |
| DELETE | `/{id}` | Supprimer |
| PATCH | `/{id}/toggle-status` | Activer/désactiver |
| GET | `/actives` | Liste des matières actives |

## 📝 Exemples de requêtes

### 1. Créer une matière
```json
POST /api/v1/matieres
{
  "code_matiere": "MATH",
  "libelle_matiere": "Mathématiques",
  "coefficient": 4,
  "est_actif": true
}
```

### 2. Liste avec filtres
```
GET /api/v1/matieres?est_actif=1&search=Math&per_page=15
```

### 3. Mettre à jour
```json
PUT /api/v1/matieres/{id}
{
  "code_matiere": "MATH",
  "libelle_matiere": "Mathématiques Générales",
  "coefficient": 5,
  "est_actif": true
}
```

## ✅ Validations

### Champs obligatoires
- `code_matiere` : max 10 caractères, unique
- `libelle_matiere` : max 200 caractères

### Champs optionnels
- `coefficient` : entier entre 1 et 10 (défaut: 2)
- `est_actif` : boolean (défaut: true)

## 🔒 Réponses API

### Succès (200/201)
```json
{
  "success": true,
  "message": "Matière créée avec succès",
  "data": {
    "id": "uuid",
    "code_matiere": "MATH",
    "libelle_matiere": "Mathématiques",
    "coefficient": 4,
    "est_actif": true,
    "created_at": "2024-01-01 10:00:00",
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
    "code_matiere": ["Ce code matière existe déjà"]
  }
}
```

### Erreur (404/500)
```json
{
  "success": false,
  "message": "Matière non trouvée"
}
```

## 📦 Tests Postman

Importer le fichier `POSTMAN_COLLECTION_MATIERES.json` dans Postman.

### Variables à configurer
- `base_url` : http://localhost:8000
- `matiere_id` : UUID de la matière

## 🔍 Logs

Tous les événements sont loggés :
- Création : `Log::info('Matière créée avec succès')`
- Mise à jour : `Log::info('Matière mise à jour avec succès')`
- Suppression : `Log::info('Matière supprimée avec succès')`
- Erreurs : `Log::error('Erreur lors de...')`

## 🎯 Fonctionnalités

✅ CRUD complet
✅ Validation stricte
✅ Gestion des erreurs
✅ Logs détaillés
✅ Pagination
✅ Filtres (statut, recherche)
✅ Soft delete via `est_actif`
✅ Relations Eloquent
✅ Réponses JSON standardisées
✅ DTOs pour la validation
✅ Service Layer
✅ Exceptions métier

## 🚦 Statuts HTTP

- `200` : Succès
- `201` : Créé
- `404` : Non trouvé
- `422` : Erreur de validation
- `500` : Erreur serveur

## 📚 Exemples de matières

```json
[
  {
    "code_matiere": "MATH",
    "libelle_matiere": "Mathématiques",
    "coefficient": 4
  },
  {
    "code_matiere": "PHYS",
    "libelle_matiere": "Physique",
    "coefficient": 3
  },
  {
    "code_matiere": "INFO",
    "libelle_matiere": "Informatique",
    "coefficient": 5
  },
  {
    "code_matiere": "FRAN",
    "libelle_matiere": "Français",
    "coefficient": 3
  },
  {
    "code_matiere": "ANG",
    "libelle_matiere": "Anglais",
    "coefficient": 2
  }
]
```
