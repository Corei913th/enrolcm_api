# Module Niveaux - API EnrolCM

## 📋 Vue d'ensemble

Module complet de gestion des niveaux académiques avec architecture Laravel standard.

## 🏗️ Architecture

### Structure des fichiers
```
app/
├── DTOs/Niveaux/
│   └── CreateNiveauDTO.php
├── Exceptions/Business/
│   └── NiveauException.php
├── Http/
│   ├── Controllers/
│   │   └── NiveauController.php
│   ├── Requests/Niveaux/
│   │   ├── StoreNiveauRequest.php
│   │   └── UpdateNiveauRequest.php
│   └── Resources/
│       └── NiveauResource.php
├── Models/
│   └── Niveau.php
└── Services/Niveaux/
    └── NiveauService.php

routes/api/
└── niveaux.php
```

## 🔗 Relations

- **Niveau → Filiere** : `belongsTo` (un niveau appartient à une filière)
- **Niveau → Matiere** : `belongsToMany` (relation many-to-many via `niveau_matiere`)

## 📊 Structure de la table `niveaux`

| Champ | Type | Description |
|-------|------|-------------|
| id | uuid | Identifiant unique |
| code_niveau | string(10) | Code unique du niveau |
| libelle_niveau | string(100) | Nom du niveau |
| filiere_id | uuid | Référence à la filière |
| ordre | integer | Ordre d'affichage |
| desc_niveau | text | Description |
| est_actif | boolean | Statut actif/inactif |

## 🚀 Endpoints API

### Base URL: `/api/v1/niveaux`

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/` | Liste paginée avec filtres |
| POST | `/` | Créer un niveau |
| GET | `/{id}` | Afficher par ID |
| GET | `/code/{code}` | Afficher par code |
| PUT | `/{id}` | Mettre à jour |
| DELETE | `/{id}` | Supprimer |
| PATCH | `/{id}/toggle-status` | Activer/désactiver |
| GET | `/actifs` | Liste des niveaux actifs |

## 📝 Exemples de requêtes

### 1. Créer un niveau
```json
POST /api/v1/niveaux
{
  "code_niveau": "L1",
  "libelle_niveau": "Licence 1",
  "filiere_id": "uuid-de-la-filiere",
  "ordre": 1,
  "desc_niveau": "Première année de licence",
  "est_actif": true
}
```

### 2. Liste avec filtres
```
GET /api/v1/niveaux?est_actif=1&filiere_id=uuid&search=Licence&per_page=15
```

### 3. Mettre à jour
```json
PUT /api/v1/niveaux/{id}
{
  "code_niveau": "L1",
  "libelle_niveau": "Licence 1 - Année Fondamentale",
  "filiere_id": "uuid-de-la-filiere",
  "ordre": 1,
  "desc_niveau": "Première année avec cours fondamentaux",
  "est_actif": true
}
```

## ✅ Validations

### Champs obligatoires
- `code_niveau` : max 10 caractères, unique
- `libelle_niveau` : max 100 caractères

### Champs optionnels
- `filiere_id` : doit exister dans la table filieres
- `ordre` : entier >= 1
- `desc_niveau` : texte libre
- `est_actif` : boolean (défaut: true)

## 🔒 Réponses API

### Succès (200/201)
```json
{
  "success": true,
  "message": "Niveau créé avec succès",
  "data": {
    "id": "uuid",
    "code_niveau": "L1",
    "libelle_niveau": "Licence 1",
    "filiere_id": "uuid",
    "ordre": 1,
    "desc_niveau": "...",
    "est_actif": true,
    "created_at": "2024-01-01 10:00:00",
    "filiere": {...},
    "matieres": [...]
  }
}
```

### Erreur de validation (422)
```json
{
  "success": false,
  "message": "Erreur de validation",
  "errors": {
    "code_niveau": ["Ce code niveau existe déjà"]
  }
}
```

### Erreur (404/500)
```json
{
  "success": false,
  "message": "Niveau non trouvé"
}
```

## 📦 Tests Postman

Importer le fichier `POSTMAN_COLLECTION_NIVEAUX.json` dans Postman.

### Variables à configurer
- `base_url` : http://localhost:8000
- `niveau_id` : UUID du niveau
- `filiere_id` : UUID de la filière

## 🔍 Logs

Tous les événements sont loggés :
- Création : `Log::info('Niveau créé avec succès')`
- Mise à jour : `Log::info('Niveau mis à jour avec succès')`
- Suppression : `Log::info('Niveau supprimé avec succès')`
- Erreurs : `Log::error('Erreur lors de...')`

## 🎯 Fonctionnalités

✅ CRUD complet
✅ Validation stricte
✅ Gestion des erreurs
✅ Logs détaillés
✅ Pagination
✅ Filtres (statut, filière, recherche)
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
