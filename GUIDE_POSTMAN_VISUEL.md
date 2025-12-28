# Guide Visuel Postman - API Écoles

## Configuration de base

**Base URL**: `http://127.0.0.1:8000/api`

---

## 1️⃣ LISTER TOUTES LES ÉCOLES

### Configuration Postman:
```
Method: GET
URL: http://127.0.0.1:8000/api/ecoles
```

### Headers:
```
Accept: application/json
```

### Réponse attendue:
```json
{
  "success": true,
  "message": "Liste des écoles récupérée avec succès",
  "data": [...],
  "meta": {
    "current_page": 1,
    "total": 5
  }
}
```

---

## 2️⃣ CRÉER UNE NOUVELLE ÉCOLE

### Configuration Postman:
```
Method: POST
URL: http://127.0.0.1:8000/api/ecoles
```

### Headers:
```
Accept: application/json
Content-Type: application/json
```

### Body (raw JSON):
```json
{
    "code_ecole": "ESSTIC",
    "libelle_ecole": "École Supérieure des Sciences et Techniques de l'Information",
    "region": "CENTRE",
    "localisation": "Yaoundé",
    "email_ecole": "contact@esstic.cm",
    "telephone_ecole": "+237222789012",
    "siteweb_ecole": "https://esstic.cm",
    "devise": "Communiquer pour Innover",
    "bp_ecole": "BP 1234",
    "est_actif": true
}
```

### Réponse attendue:
```json
{
  "success": true,
  "message": "École créée avec succès",
  "data": {
    "id": "019b65ea-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
    "code_ecole": "ESSTIC",
    "libelle_ecole": "École Supérieure des Sciences et Techniques de l'Information",
    ...
  }
}
```

**💡 Note**: Copie l'ID retourné pour les prochaines requêtes!

---

## 3️⃣ VOIR UNE ÉCOLE PAR ID

### Configuration Postman:
```
Method: GET
URL: http://127.0.0.1:8000/api/ecoles/019b65e9-61b8-72f0-bad3-077d054ab5f2
```
*(Remplace l'ID par celui de ton école)*

### Headers:
```
Accept: application/json
```

---

## 4️⃣ VOIR UNE ÉCOLE PAR CODE

### Configuration Postman:
```
Method: GET
URL: http://127.0.0.1:8000/api/ecoles/code/ENSP
```

### Headers:
```
Accept: application/json
```

---

## 5️⃣ MODIFIER UNE ÉCOLE

### Configuration Postman:
```
Method: PUT
URL: http://127.0.0.1:8000/api/ecoles/019b65e9-61b8-72f0-bad3-077d054ab5f2
```
*(Remplace l'ID)*

### Headers:
```
Accept: application/json
Content-Type: application/json
```

### Body (raw JSON):
```json
{
    "libelle_ecole": "École Supérieure - Mise à jour",
    "email_ecole": "nouveau@esstic.cm",
    "telephone_ecole": "+237222999999"
}
```

---

## 6️⃣ ACTIVER/DÉSACTIVER UNE ÉCOLE

### Configuration Postman:
```
Method: PATCH
URL: http://127.0.0.1:8000/api/ecoles/019b65e9-61b8-72f0-bad3-077d054ab5f2/toggle-status
```
*(Remplace l'ID)*

### Headers:
```
Accept: application/json
```

---

## 7️⃣ UPLOAD LOGO (IMPORTANT!)

### Configuration Postman:
```
Method: POST
URL: http://127.0.0.1:8000/api/ecoles/019b65e9-61b8-72f0-bad3-077d054ab5f2/upload-file
```
*(Remplace l'ID)*

### Headers:
```
Accept: application/json
```
**⚠️ NE PAS mettre Content-Type (Postman le gère automatiquement)**

### Body:
**Sélectionne "form-data" (pas raw!)**

| KEY  | VALUE | TYPE |
|------|-------|------|
| file | [Select File] | File |
| type | logo | Text |

**Instructions détaillées:**
1. Dans Postman, clique sur l'onglet **"Body"**
2. Sélectionne **"form-data"** (radio button)
3. Première ligne:
   - KEY: `file`
   - Passe le curseur sur la colonne VALUE, un dropdown apparaît
   - Sélectionne **"File"** dans le dropdown
   - Clique sur "Select Files" et choisis ton image (logo.png, logo.jpg, etc.)
4. Deuxième ligne:
   - KEY: `type`
   - VALUE: `logo` (reste sur "Text")
   - Type: Text

### Réponse attendue:
```json
{
  "success": true,
  "message": "Fichier uploadé avec succès",
  "data": {
    "id": "...",
    "logo_path": "ecoles/logos/xxx.png",
    "logo_original_name": "logo.png",
    ...
  }
}
```

---

## 8️⃣ UPLOAD EMBLÈME

### Configuration Postman:
```
Method: POST
URL: http://127.0.0.1:8000/api/ecoles/019b65e9-61b8-72f0-bad3-077d054ab5f2/upload-file
```

### Headers:
```
Accept: application/json
```

### Body (form-data):

| KEY  | VALUE | TYPE |
|------|-------|------|
| file | [Select File] | File |
| type | embleme | Text |

---

## 9️⃣ SUPPRIMER UN FICHIER

### Configuration Postman:
```
Method: DELETE
URL: http://127.0.0.1:8000/api/ecoles/019b65e9-61b8-72f0-bad3-077d054ab5f2/delete-file
```

### Headers:
```
Accept: application/json
Content-Type: application/json
```

### Body (raw JSON):
```json
{
    "type": "logo"
}
```

**Types possibles**: `logo`, `embleme`, `header_frame`

---

## 🔟 SUPPRIMER UNE ÉCOLE

### Configuration Postman:
```
Method: DELETE
URL: http://127.0.0.1:8000/api/ecoles/019b65e9-61b8-72f0-bad3-077d054ab5f2
```

### Headers:
```
Accept: application/json
```

---

## 📋 SCÉNARIO COMPLET DE TEST

### Étape 1: Lister les écoles existantes
```
GET http://127.0.0.1:8000/api/ecoles
```

### Étape 2: Créer une nouvelle école
```
POST http://127.0.0.1:8000/api/ecoles
Body: {
    "code_ecole": "TEST2024",
    "libelle_ecole": "École de Test",
    "region": "CENTRE",
    "localisation": "Yaoundé",
    "email_ecole": "test@ecole.cm",
    "telephone_ecole": "+237222000000",
    "est_actif": true
}
```
**→ Copie l'ID retourné (ex: 019b65ea-1234-5678-9abc-def012345678)**

### Étape 3: Voir l'école créée
```
GET http://127.0.0.1:8000/api/ecoles/019b65ea-1234-5678-9abc-def012345678
```

### Étape 4: Upload un logo
```
POST http://127.0.0.1:8000/api/ecoles/019b65ea-1234-5678-9abc-def012345678/upload-file
Body (form-data):
- file: [ton fichier image]
- type: logo
```

### Étape 5: Modifier l'école
```
PUT http://127.0.0.1:8000/api/ecoles/019b65ea-1234-5678-9abc-def012345678
Body: {
    "libelle_ecole": "École de Test - Modifiée",
    "email_ecole": "nouveau@test.cm"
}
```

### Étape 6: Désactiver l'école
```
PATCH http://127.0.0.1:8000/api/ecoles/019b65ea-1234-5678-9abc-def012345678/toggle-status
```

### Étape 7: Supprimer l'école
```
DELETE http://127.0.0.1:8000/api/ecoles/019b65ea-1234-5678-9abc-def012345678
```

---

## ⚠️ ERREURS COURANTES

### Erreur: "The selected region is invalid"
**Solution**: Utilise les valeurs en MAJUSCULES:
- ✅ `"region": "CENTRE"`
- ❌ `"region": "Centre"`

Valeurs valides: `CENTRE`, `LITTORAL`, `OUEST`, `SUD`, `EST`, `NORD`, `ADAMAOUA`, `NORD_OUEST`, `SUD_OUEST`, `EXTREME_NORD`

### Erreur: "The type field is required"
**Solution**: Vérifie que tu utilises bien `type` (pas `file_type`)

### Erreur: "The file field is required"
**Solution**: 
1. Assure-toi d'utiliser **form-data** (pas raw)
2. Le champ doit être de type **File** (pas Text)
3. Sélectionne bien un fichier image

### Erreur: 422 Unprocessable Content
**Solution**: Vérifie que tous les champs requis sont présents:
- `code_ecole` (requis, unique)
- `libelle_ecole` (requis)
- `region` (requis, en MAJUSCULES)

---

## 🎯 ASTUCE POSTMAN

### Utiliser des variables
1. Crée une variable `ecole_id` dans l'environnement Postman
2. Dans le test de création (POST), ajoute ce script:
```javascript
if (pm.response.code === 201) {
    var jsonData = pm.response.json();
    pm.environment.set("ecole_id", jsonData.data.id);
}
```
3. Utilise `{{ecole_id}}` dans tes URLs:
```
http://127.0.0.1:8000/api/ecoles/{{ecole_id}}
```

---

## 📝 NOTES IMPORTANTES

1. **Authentification**: Actuellement désactivée pour les tests
2. **Formats d'images acceptés**: PNG, JPG, JPEG
3. **Taille max des fichiers**: 5MB
4. **Base URL**: Assure-toi que le serveur Laravel tourne sur `http://127.0.0.1:8000`
5. **IDs**: Ce sont des UUIDs, pas des entiers
