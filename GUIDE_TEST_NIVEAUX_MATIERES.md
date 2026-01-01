# 🧪 Guide de Test - Modules Niveaux & Matières

## 🎯 Objectif
Tester rapidement les deux nouveaux modules pour vérifier leur bon fonctionnement.

---

## 📋 Prérequis

1. **Base de données** configurée et migrée
2. **Serveur Laravel** démarré : `php artisan serve`
3. **Postman** installé (ou tout autre client HTTP)
4. **Données de test** : avoir au moins une filière créée pour tester les niveaux

---

## 🚀 Tests Module Niveau

### 1. Créer un niveau (POST)

**Endpoint** : `POST http://localhost:8000/api/v1/niveaux`

**Headers** :
```
Content-Type: application/json
Accept: application/json
```

**Body** :
```json
{
  "code_niveau": "L1",
  "libelle_niveau": "Licence 1",
  "filiere_id": null,
  "ordre": 1,
  "desc_niveau": "Première année de licence",
  "est_actif": true
}
```

**Résultat attendu** : Status 201, niveau créé avec UUID

---

### 2. Liste des niveaux (GET)

**Endpoint** : `GET http://localhost:8000/api/v1/niveaux`

**Résultat attendu** : Status 200, liste paginée des niveaux

---

### 3. Afficher un niveau (GET)

**Endpoint** : `GET http://localhost:8000/api/v1/niveaux/{id}`

Remplacer `{id}` par l'UUID du niveau créé

**Résultat attendu** : Status 200, détails du niveau

---

### 4. Afficher par code (GET)

**Endpoint** : `GET http://localhost:8000/api/v1/niveaux/code/L1`

**Résultat attendu** : Status 200, niveau avec code "L1"

---

### 5. Mettre à jour (PUT)

**Endpoint** : `PUT http://localhost:8000/api/v1/niveaux/{id}`

**Body** :
```json
{
  "code_niveau": "L1",
  "libelle_niveau": "Licence 1 - Année Fondamentale",
  "filiere_id": null,
  "ordre": 1,
  "desc_niveau": "Première année avec cours fondamentaux",
  "est_actif": true
}
```

**Résultat attendu** : Status 200, niveau mis à jour

---

### 6. Toggle statut (PATCH)

**Endpoint** : `PATCH http://localhost:8000/api/v1/niveaux/{id}/toggle-status`

**Résultat attendu** : Status 200, `est_actif` inversé

---

### 7. Liste des niveaux actifs (GET)

**Endpoint** : `GET http://localhost:8000/api/v1/niveaux/actifs`

**Résultat attendu** : Status 200, uniquement les niveaux actifs

---

### 8. Supprimer (DELETE)

**Endpoint** : `DELETE http://localhost:8000/api/v1/niveaux/{id}`

**Résultat attendu** : Status 200, niveau supprimé

---

## 🎨 Tests Module Matière

### 1. Créer une matière (POST)

**Endpoint** : `POST http://localhost:8000/api/v1/matieres`

**Headers** :
```
Content-Type: application/json
Accept: application/json
```

**Body** :
```json
{
  "code_matiere": "MATH",
  "libelle_matiere": "Mathématiques",
  "coefficient": 4,
  "est_actif": true
}
```

**Résultat attendu** : Status 201, matière créée avec UUID

---

### 2. Liste des matières (GET)

**Endpoint** : `GET http://localhost:8000/api/v1/matieres`

**Résultat attendu** : Status 200, liste paginée des matières

---

### 3. Afficher une matière (GET)

**Endpoint** : `GET http://localhost:8000/api/v1/matieres/{id}`

Remplacer `{id}` par l'UUID de la matière créée

**Résultat attendu** : Status 200, détails de la matière

---

### 4. Afficher par code (GET)

**Endpoint** : `GET http://localhost:8000/api/v1/matieres/code/MATH`

**Résultat attendu** : Status 200, matière avec code "MATH"

---

### 5. Mettre à jour (PUT)

**Endpoint** : `PUT http://localhost:8000/api/v1/matieres/{id}`

**Body** :
```json
{
  "code_matiere": "MATH",
  "libelle_matiere": "Mathématiques Générales",
  "coefficient": 5,
  "est_actif": true
}
```

**Résultat attendu** : Status 200, matière mise à jour

---

### 6. Toggle statut (PATCH)

**Endpoint** : `PATCH http://localhost:8000/api/v1/matieres/{id}/toggle-status`

**Résultat attendu** : Status 200, `est_actif` inversé

---

### 7. Liste des matières actives (GET)

**Endpoint** : `GET http://localhost:8000/api/v1/matieres/actives`

**Résultat attendu** : Status 200, uniquement les matières actives

---

### 8. Supprimer (DELETE)

**Endpoint** : `DELETE http://localhost:8000/api/v1/matieres/{id}`

**Résultat attendu** : Status 200, matière supprimée

---

## ✅ Tests de validation

### Test 1 : Code unique (Niveau)

**Créer deux niveaux avec le même code**

**Résultat attendu** : 
- Premier : Status 201 ✅
- Deuxième : Status 422 avec message "Ce code niveau existe déjà"

---

### Test 2 : Code unique (Matière)

**Créer deux matières avec le même code**

**Résultat attendu** : 
- Premier : Status 201 ✅
- Deuxième : Status 422 avec message "Ce code matière existe déjà"

---

### Test 3 : Champs obligatoires (Niveau)

**Body invalide** :
```json
{
  "libelle_niveau": "Licence 1"
}
```

**Résultat attendu** : Status 422 avec erreur sur `code_niveau`

---

### Test 4 : Champs obligatoires (Matière)

**Body invalide** :
```json
{
  "libelle_matiere": "Mathématiques"
}
```

**Résultat attendu** : Status 422 avec erreur sur `code_matiere`

---

### Test 5 : Coefficient invalide

**Body invalide** :
```json
{
  "code_matiere": "TEST",
  "libelle_matiere": "Test",
  "coefficient": 15
}
```

**Résultat attendu** : Status 422 avec erreur "Le coefficient ne peut pas dépasser 10"

---

## 🔍 Tests de filtres

### Filtre par statut (Niveau)
```
GET /api/v1/niveaux?est_actif=1
```

### Filtre par recherche (Niveau)
```
GET /api/v1/niveaux?search=Licence
```

### Filtre par filière (Niveau)
```
GET /api/v1/niveaux?filiere_id={uuid}
```

### Filtre par statut (Matière)
```
GET /api/v1/matieres?est_actif=1
```

### Filtre par recherche (Matière)
```
GET /api/v1/matieres?search=Math
```

---

## 📊 Vérification des logs

Après chaque opération, vérifier les logs Laravel :

```bash
tail -f storage/logs/laravel.log
```

**Logs attendus** :
- `Niveau créé avec succès`
- `Matière créée avec succès`
- `Niveau mis à jour avec succès`
- `Matière mise à jour avec succès`
- `Statut du niveau modifié`
- `Statut de la matière modifié`
- `Niveau supprimé avec succès`
- `Matière supprimée avec succès`

---

## 🗄️ Vérification en base de données

### Vérifier les niveaux créés
```sql
SELECT * FROM niveaux;
```

### Vérifier les matières créées
```sql
SELECT * FROM matieres;
```

---

## 🎯 Checklist complète

### Module Niveau
- [ ] Créer un niveau
- [ ] Lister les niveaux
- [ ] Afficher un niveau par ID
- [ ] Afficher un niveau par code
- [ ] Mettre à jour un niveau
- [ ] Toggle statut
- [ ] Lister les niveaux actifs
- [ ] Supprimer un niveau
- [ ] Validation code unique
- [ ] Validation champs obligatoires
- [ ] Filtres fonctionnels
- [ ] Logs présents

### Module Matière
- [ ] Créer une matière
- [ ] Lister les matières
- [ ] Afficher une matière par ID
- [ ] Afficher une matière par code
- [ ] Mettre à jour une matière
- [ ] Toggle statut
- [ ] Lister les matières actives
- [ ] Supprimer une matière
- [ ] Validation code unique
- [ ] Validation champs obligatoires
- [ ] Validation coefficient (1-10)
- [ ] Filtres fonctionnels
- [ ] Logs présents

---

## 🚨 Erreurs courantes

### Erreur 404 sur les routes
**Solution** : Vérifier que les routes sont bien incluses dans `routes/api.php`

### Erreur 500 sur création
**Solution** : Vérifier les migrations et que la table existe

### Validation ne fonctionne pas
**Solution** : Vérifier les FormRequests et les règles de validation

### Relations non chargées
**Solution** : Vérifier les `with()` dans les services

---

## 📦 Import Postman rapide

1. Ouvrir Postman
2. Import → File
3. Sélectionner `POSTMAN_COLLECTION_NIVEAUX.json`
4. Sélectionner `POSTMAN_COLLECTION_MATIERES.json`
5. Configurer les variables d'environnement
6. Lancer les tests

---

## ✨ Résultat attendu

Si tous les tests passent :
- ✅ Les deux modules sont fonctionnels
- ✅ L'architecture est cohérente
- ✅ Les validations fonctionnent
- ✅ Les logs sont présents
- ✅ Les filtres fonctionnent
- ✅ Les relations sont correctes

**Les branches sont prêtes à merger !** 🎉

---

**Durée estimée des tests** : 15-20 minutes par module
