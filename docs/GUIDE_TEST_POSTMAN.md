# 🧪 Guide de Test Postman - Module Écoles avec Fichiers

## 📋 Prérequis

1. **Serveur Laravel lancé**
```bash
php artisan serve
```

2. **Migration exécutée**
```bash
php artisan migrate
```

3. **Token d'authentification**
```bash
# Créer un utilisateur de test
php artisan tinker
>>> $user = App\Models\User::factory()->create(['email' => 'test@example.com']);
>>> $token = $user->createToken('test-token')->plainTextToken;
>>> echo $token;
```

4. **Fichiers de test préparés**
- `logo.png` (max 2MB)
- `embleme.png` (max 2MB)
- `header.jpg` (max 5MB)

---

## 🎯 Test 1 : Créer une École SANS Fichiers

### Configuration
```
Method: POST
URL: http://localhost:8000/api/ecoles
Headers:
  - Authorization: Bearer {VOTRE_TOKEN}
  - Content-Type: application/json
```

### Body (JSON)
```json
{
  "code_ecole": "TEST001",
  "libelle_ecole": "École de Test",
  "libelle_ecole_en": "Test School",
  "region": "CENTRE",
  "localisation": "Yaoundé",
  "devise": "Excellence et Innovation",
  "bp_ecole": "BP 1234",
  "email_ecole": "contact@test.cm",
  "telephone_ecole": "+237222000000",
  "directeur_nom": "Dr. Test DIRECTEUR",
  "directeur_email": "directeur@test.cm",
  "type_etablissement": "public",
  "est_actif": true
}
```

### Réponse Attendue (201 Created)
```json
{
  "success": true,
  "message": "École créée avec succès",
  "data": {
    "id": "uuid-de-l-ecole",
    "code_ecole": "TEST001",
    "libelle_ecole": "École de Test",
    "logo_path": null,
    "embleme_path": null,
    "header_frame_path": null,
    ...
  }
}
```

**✅ Vérification :** Notez l'`id` de l'école créée pour les tests suivants.

---

## 🎯 Test 2 : Créer une École AVEC Fichiers

### Configuration
```
Method: POST
URL: http://localhost:8000/api/ecoles
Headers:
  - Authorization: Bearer {VOTRE_TOKEN}
  - Content-Type: multipart/form-data
```

### Body (form-data)
```
code_ecole: ENSP
libelle_ecole: École Nationale Supérieure Polytechnique
libelle_ecole_en: National Advanced School of Engineering
region: CENTRE
localisation: Yaoundé
devise: Excellence et Innovation
bp_ecole: BP 8390
email_ecole: contact@ensp.cm
telephone_ecole: +237222234567
directeur_nom: Pr. Jean KOULIDIATI
directeur_email: directeur@ensp.cm
directeur_telephone: +237222234568
type_etablissement: public
numero_agrement: AGR-ENSP-001
date_creation: 1971-01-01
logo: [FILE] logo.png
embleme: [FILE] embleme.png
header_frame: [FILE] header.jpg
```

### Réponse Attendue (201 Created)
```json
{
  "success": true,
  "message": "École créée avec succès",
  "data": {
    "id": "uuid-de-l-ecole",
    "code_ecole": "ENSP",
    "libelle_ecole": "École Nationale Supérieure Polytechnique",
    "logo_path": "ecoles/{uuid}/logo_20241228143022_a8f3d9e2.png",
    "logo_url": "http://localhost:8000/storage/ecoles/{uuid}/logo_20241228143022_a8f3d9e2.png",
    "embleme_path": "ecoles/{uuid}/embleme_20241228143045_b7c4e1f3.png",
    "header_frame_path": "ecoles/{uuid}/header_frame_20241228143102_c9d5f2g4.jpg",
    ...
  }
}
```

**✅ Vérifications :**
1. Les chemins des fichiers sont présents
2. Les URLs sont accessibles
3. Notez l'`id` pour les tests suivants

---

## 🎯 Test 3 : Lister les Écoles

### Configuration
```
Method: GET
URL: http://localhost:8000/api/ecoles
Headers:
  - Authorization: Bearer {VOTRE_TOKEN}
```

### Réponse Attendue (200 OK)
```json
{
  "success": true,
  "message": "Liste des écoles récupérée avec succès",
  "data": [
    {
      "id": "uuid",
      "code_ecole": "ENSP",
      "libelle_ecole": "École Nationale...",
      "logo_url": "http://localhost:8000/storage/ecoles/...",
      ...
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 2
  }
}
```

**✅ Vérification :** Vous devez voir les 2 écoles créées.

---

## 🎯 Test 4 : Récupérer une École par ID

### Configuration
```
Method: GET
URL: http://localhost:8000/api/ecoles/{ID_ECOLE}
Headers:
  - Authorization: Bearer {VOTRE_TOKEN}
```

### Réponse Attendue (200 OK)
```json
{
  "success": true,
  "message": "École récupérée avec succès",
  "data": {
    "id": "uuid",
    "code_ecole": "ENSP",
    "logo_path": "ecoles/{uuid}/logo_...",
    "logo_url": "http://localhost:8000/storage/...",
    ...
  }
}
```

**✅ Vérification :** Copiez une des URLs de fichier et ouvrez-la dans le navigateur pour vérifier que l'image s'affiche.

---

## 🎯 Test 5 : Récupérer une École par Code

### Configuration
```
Method: GET
URL: http://localhost:8000/api/ecoles/code/ENSP
Headers:
  - Authorization: Bearer {VOTRE_TOKEN}
```

### Réponse Attendue (200 OK)
```json
{
  "success": true,
  "message": "École récupérée avec succès",
  "data": {
    "code_ecole": "ENSP",
    ...
  }
}
```

---

## 🎯 Test 6 : Uploader un Fichier Spécifique

### Configuration
```
Method: POST
URL: http://localhost:8000/api/ecoles/{ID_ECOLE}/upload-file
Headers:
  - Authorization: Bearer {VOTRE_TOKEN}
  - Content-Type: multipart/form-data
```

### Body (form-data)
```
type: logo
file: [FILE] nouveau_logo.png
```

### Réponse Attendue (200 OK)
```json
{
  "success": true,
  "message": "Fichier uploadé avec succès",
  "data": {
    "id": "uuid",
    "logo_path": "ecoles/{uuid}/logo_20241228150000_xyz.png",
    "logo_url": "http://localhost:8000/storage/...",
    "logo_original_name": "nouveau_logo.png",
    ...
  }
}
```

**✅ Vérification :** L'ancien fichier doit être supprimé automatiquement du serveur.

---

## 🎯 Test 7 : Mettre à Jour une École avec Fichiers

### Configuration
```
Method: PUT
URL: http://localhost:8000/api/ecoles/{ID_ECOLE}
Headers:
  - Authorization: Bearer {VOTRE_TOKEN}
  - Content-Type: multipart/form-data
```

### Body (form-data)
```
libelle_ecole: École Nationale Supérieure Polytechnique de Yaoundé
devise: Excellence, Innovation et Leadership
logo: [FILE] logo_updated.png
```

### Réponse Attendue (200 OK)
```json
{
  "success": true,
  "message": "École mise à jour avec succès",
  "data": {
    "libelle_ecole": "École Nationale Supérieure Polytechnique de Yaoundé",
    "devise": "Excellence, Innovation et Leadership",
    "logo_path": "ecoles/{uuid}/logo_20241228151000_abc.png",
    ...
  }
}
```

---

## 🎯 Test 8 : Générer une Attestation PDF

### Configuration
```
Method: POST
URL: http://localhost:8000/api/ecoles/{ID_ECOLE}/generate-attestation
Headers:
  - Authorization: Bearer {VOTRE_TOKEN}
  - Content-Type: application/json
```

### Body (JSON)
```json
{
  "etudiant_nom": "Jean DUPONT",
  "numero": "ATT-2024-001",
  "date_naissance": "1995-05-15",
  "lieu_naissance": "Yaoundé",
  "contenu": "a été régulièrement inscrit et a suivi avec assiduité les cours de l'année académique 2023-2024."
}
```

### Réponse Attendue
Un fichier PDF téléchargé automatiquement : `attestation_ATT-2024-001.pdf`

**✅ Vérifications :**
1. Le PDF se télécharge
2. L'entête contient le logo et l'emblème
3. Les informations de l'école sont présentes
4. Le contenu de l'attestation est correct

---

## 🎯 Test 9 : Prévisualiser l'Entête PDF

### Configuration
```
Method: GET
URL: http://localhost:8000/api/ecoles/{ID_ECOLE}/preview-header
Headers:
  - Authorization: Bearer {VOTRE_TOKEN}
```

### Réponse Attendue
Un PDF s'ouvre dans le navigateur avec l'entête officielle de l'école.

**✅ Vérifications :**
1. Le logo est visible
2. L'emblème est visible
3. Les informations (nom, devise, contacts) sont présentes
4. Le cadre décoratif est affiché

---

## 🎯 Test 10 : Supprimer un Fichier

### Configuration
```
Method: DELETE
URL: http://localhost:8000/api/ecoles/{ID_ECOLE}/delete-file
Headers:
  - Authorization: Bearer {VOTRE_TOKEN}
  - Content-Type: application/json
```

### Body (JSON)
```json
{
  "type": "logo"
}
```

### Réponse Attendue (200 OK)
```json
{
  "success": true,
  "message": "Fichier supprimé avec succès",
  "data": {
    "logo_path": null,
    "logo_original_name": null,
    ...
  }
}
```

**✅ Vérification :** Le fichier doit être supprimé du serveur.

---

## 🎯 Test 11 : Supprimer une École

### Configuration
```
Method: DELETE
URL: http://localhost:8000/api/ecoles/{ID_ECOLE}
Headers:
  - Authorization: Bearer {VOTRE_TOKEN}
```

### Réponse Attendue (200 OK)
```json
{
  "success": true,
  "message": "École et fichiers supprimés avec succès"
}
```

**✅ Vérifications :**
1. L'école est supprimée de la base de données
2. Tous les fichiers sont supprimés du serveur
3. Le dossier `storage/app/public/ecoles/{uuid}` est supprimé

---

## 🎯 Test 12 : Validation des Erreurs

### Test 12.1 : Fichier trop volumineux
```
Method: POST
URL: http://localhost:8000/api/ecoles/{ID_ECOLE}/upload-file
Body (form-data):
  type: logo
  file: [FILE] logo_10MB.png (fichier > 2MB)
```

**Réponse Attendue (422)**
```json
{
  "success": false,
  "message": "Erreur de validation",
  "errors": {
    "file": ["Le fichier ne doit pas dépasser 2048 Ko."]
  }
}
```

### Test 12.2 : Type de fichier invalide
```
Body (form-data):
  type: logo
  file: [FILE] document.pdf
```

**Réponse Attendue (422)**
```json
{
  "success": false,
  "message": "Erreur de validation",
  "errors": {
    "file": ["Le fichier doit être une image."]
  }
}
```

### Test 12.3 : Code école déjà existant
```
Method: POST
URL: http://localhost:8000/api/ecoles
Body (JSON):
{
  "code_ecole": "ENSP",  // Code déjà utilisé
  "libelle_ecole": "Test",
  "region": "CENTRE"
}
```

**Réponse Attendue (422)**
```json
{
  "success": false,
  "message": "Erreur de validation",
  "errors": {
    "code_ecole": ["Ce code école existe déjà"]
  }
}
```

---

## 📊 Vérifications Finales

### 1. Vérifier le Stockage
```bash
# Windows
dir storage\app\public\ecoles

# Linux/Mac
ls -la storage/app/public/ecoles/
```

Vous devriez voir :
```
ecoles/
  └── {uuid}/
      ├── logo_20241228_xxx.png
      ├── embleme_20241228_xxx.png
      └── header_frame_20241228_xxx.jpg
```

### 2. Vérifier les URLs Publiques
Ouvrez dans le navigateur :
```
http://localhost:8000/storage/ecoles/{uuid}/logo_xxx.png
```

L'image doit s'afficher.

### 3. Vérifier la Base de Données
```bash
php artisan tinker
>>> App\Models\Ecole::with('departements')->first();
```

Vous devriez voir les champs `logo_path`, `embleme_path`, etc.

---

## ✅ Checklist de Validation

- [ ] Création d'école sans fichiers fonctionne
- [ ] Création d'école avec fichiers fonctionne
- [ ] Les fichiers sont stockés dans le bon dossier
- [ ] Les URLs des fichiers sont accessibles
- [ ] Upload de fichier individuel fonctionne
- [ ] Mise à jour avec fichiers fonctionne
- [ ] Les anciens fichiers sont supprimés automatiquement
- [ ] Génération de PDF avec entête fonctionne
- [ ] Le PDF contient logo + emblème
- [ ] Prévisualisation de l'entête fonctionne
- [ ] Suppression de fichier fonctionne
- [ ] Suppression d'école supprime les fichiers
- [ ] Validation des types de fichiers fonctionne
- [ ] Validation des tailles fonctionne
- [ ] Messages d'erreur en français
- [ ] Récupération par code fonctionne

---

## 🐛 Dépannage

### Problème : "Storage link not found"
```bash
php artisan storage:link
```

### Problème : "Permission denied"
```bash
# Windows
icacls storage /grant Users:F /T

# Linux/Mac
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Problème : "Class EcoleData not found"
```bash
composer dump-autoload
```

### Problème : PDF ne se génère pas
```bash
composer require barryvdh/laravel-dompdf
php artisan config:clear
```

---

## 🎉 Résultat Attendu

Si tous les tests passent, vous avez :
✅ Un système complet de gestion des fichiers  
✅ Une génération de PDF professionnelle  
✅ Une API REST fonctionnelle  
✅ Une validation stricte  
✅ Une sécurité optimale  

**Votre système est production-ready !** 🚀
