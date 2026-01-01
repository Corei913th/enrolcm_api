# ⚡ Quick Start - Modules Niveaux & Matières

## 🎯 Démarrage rapide en 5 minutes

---

## 1️⃣ Vérifier les branches

```bash
git branch -v
```

Vous devriez voir :
- ✅ `feature/niveau` - Module Niveaux
- ✅ `feature/matiere` - Module Matières

---

## 2️⃣ Tester le module Niveau

### Basculer sur la branche
```bash
git checkout feature/niveau
```

### Démarrer le serveur
```bash
php artisan serve
```

### Test rapide avec curl
```bash
# Créer un niveau
curl -X POST http://localhost:8000/api/v1/niveaux \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "code_niveau": "L1",
    "libelle_niveau": "Licence 1",
    "ordre": 1,
    "est_actif": true
  }'

# Lister les niveaux
curl http://localhost:8000/api/v1/niveaux
```

### Ou avec Postman
1. Importer `POSTMAN_COLLECTION_NIVEAUX.json`
2. Configurer `base_url` = `http://localhost:8000`
3. Lancer "1. Créer un niveau"
4. Lancer "2. Liste des niveaux"

---

## 3️⃣ Tester le module Matière

### Basculer sur la branche
```bash
git checkout feature/matiere
```

### Test rapide avec curl
```bash
# Créer une matière
curl -X POST http://localhost:8000/api/v1/matieres \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "code_matiere": "MATH",
    "libelle_matiere": "Mathématiques",
    "coefficient": 4,
    "est_actif": true
  }'

# Lister les matières
curl http://localhost:8000/api/v1/matieres
```

### Ou avec Postman
1. Importer `POSTMAN_COLLECTION_MATIERES.json`
2. Configurer `base_url` = `http://localhost:8000`
3. Lancer "1. Créer une matière"
4. Lancer "2. Liste des matières"

---

## 4️⃣ Vérifier les logs

```bash
tail -f storage/logs/laravel.log
```

Vous devriez voir :
- `Niveau créé avec succès`
- `Matière créée avec succès`

---

## 5️⃣ Vérifier en base de données

```bash
php artisan tinker
```

```php
// Compter les niveaux
\App\Models\Niveau::count();

// Compter les matières
\App\Models\Matiere::count();

// Voir le dernier niveau
\App\Models\Niveau::latest()->first();

// Voir la dernière matière
\App\Models\Matiere::latest()->first();
```

---

## ✅ Si tout fonctionne

Vous êtes prêt à merger ! 🎉

```bash
# Option 1 : Merger dans main
git checkout main
git merge feature/niveau --no-ff
git merge feature/matiere --no-ff

# Option 2 : Push et créer des Pull Requests
git push origin feature/niveau
git push origin feature/matiere
```

---

## 📚 Documentation complète

- **README_NIVEAUX.md** - Guide complet Niveau
- **README_MATIERES.md** - Guide complet Matière
- **GUIDE_TEST_NIVEAUX_MATIERES.md** - Tests détaillés
- **IMPLEMENTATION_NIVEAUX_MATIERES.md** - Vue technique
- **RESUME_FINAL_NIVEAUX_MATIERES.md** - Résumé complet
- **BRANCHES_OVERVIEW.md** - Vue d'ensemble

---

## 🆘 Problèmes courants

### Routes 404
```bash
php artisan route:list | grep niveaux
php artisan route:list | grep matieres
```

### Migrations manquantes
```bash
php artisan migrate:status
php artisan migrate
```

### Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 🎯 Endpoints disponibles

### Niveau
- `GET /api/v1/niveaux` - Liste
- `POST /api/v1/niveaux` - Créer
- `GET /api/v1/niveaux/{id}` - Afficher
- `PUT /api/v1/niveaux/{id}` - Modifier
- `DELETE /api/v1/niveaux/{id}` - Supprimer

### Matière
- `GET /api/v1/matieres` - Liste
- `POST /api/v1/matieres` - Créer
- `GET /api/v1/matieres/{id}` - Afficher
- `PUT /api/v1/matieres/{id}` - Modifier
- `DELETE /api/v1/matieres/{id}` - Supprimer

---

**Temps estimé** : 5 minutes par module
**Difficulté** : ⭐ Facile
