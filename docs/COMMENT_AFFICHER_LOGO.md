# Comment faire afficher le logo dans le PDF

## Prérequis
1. ✅ Le serveur Laravel doit tourner: `php artisan serve`
2. ✅ Une école doit exister dans la base de données
3. ✅ Un fichier image (logo) doit être disponible sur ton ordinateur

## Méthode 1: Avec Postman (Recommandé)

### Étape 1: Récupérer l'ID d'une école
```
GET http://127.0.0.1:8000/api/ecoles
```
Copie l'`id` d'une école (ex: `019b65e9-61d8-7291-b7b1-b208bd625079`)

### Étape 2: Uploader le logo
```
POST http://127.0.0.1:8000/api/ecoles/{ID}/upload-file
```

**Configuration Postman:**
1. Sélectionne **POST**
2. URL: `http://127.0.0.1:8000/api/ecoles/019b65e9-61d8-7291-b7b1-b208bd625079/upload-file`
3. Headers:
   - `Accept: application/json`
4. Body: Sélectionne **form-data**
5. Ajoute deux champs:
   
   | KEY  | VALUE | TYPE |
   |------|-------|------|
   | file | [Clique "Select Files" et choisis ton image] | File |
   | type | logo | Text |

6. Clique **Send**

**Réponse attendue:**
```json
{
  "success": true,
  "message": "Fichier uploadé avec succès",
  "data": {
    "id": "...",
    "logo_path": "ecoles/logos/xxx.jpg",
    ...
  }
}
```

### Étape 3: Générer le PDF
```
GET http://127.0.0.1:8000/api/ecoles/{ID}/preview-header
```

Le PDF s'ouvrira dans ton navigateur avec le logo affiché!

---

## Méthode 2: Avec PowerShell

### Étape 1: Modifier le script
Ouvre `test-upload-logo.ps1` et modifie:
```powershell
$ecoleId = "019b65e9-61d8-7291-b7b1-b208bd625079"  # Ton ID d'école
$logoPath = "C:/Users/adolp/Desktop/logo.jpeg"     # Chemin vers ton image
```

### Étape 2: Exécuter le script
```powershell
.\test-upload-logo.ps1
```

Le script va:
1. ✅ Vérifier que l'école existe
2. ✅ Vérifier que le fichier logo existe
3. ✅ Uploader le logo
4. ✅ Générer le PDF
5. ✅ Ouvrir le PDF automatiquement

---

## Méthode 3: Avec curl (Linux/Mac)

### Upload du logo
```bash
curl -X POST "http://127.0.0.1:8000/api/ecoles/{ID}/upload-file" \
  -H "Accept: application/json" \
  -F "file=@/path/to/logo.jpg" \
  -F "type=logo"
```

### Générer le PDF
```bash
curl -X GET "http://127.0.0.1:8000/api/ecoles/{ID}/preview-header" \
  --output preview.pdf

# Ouvrir le PDF
open preview.pdf  # Mac
xdg-open preview.pdf  # Linux
```

---

## Vérification que ça fonctionne

### 1. Vérifier que le fichier est uploadé
```
GET http://127.0.0.1:8000/api/ecoles/{ID}
```

Dans la réponse, tu devrais voir:
```json
{
  "logo_path": "ecoles/logos/xxx.jpg",
  "logo_original_name": "logo.jpg",
  "logo_url": "http://127.0.0.1:8000/storage/ecoles/logos/xxx.jpg"
}
```

### 2. Vérifier que le fichier existe physiquement
Le fichier doit être dans:
```
storage/app/public/ecoles/logos/xxx.jpg
```

### 3. Vérifier le lien symbolique
Si le fichier n'est pas accessible via l'URL, crée le lien symbolique:
```bash
php artisan storage:link
```

---

## Dépannage

### Le logo ne s'affiche pas dans le PDF

**Problème 1: Le fichier n'est pas uploadé**
- Vérifie la réponse de l'upload
- Vérifie que le fichier existe dans `storage/app/public/ecoles/logos/`

**Problème 2: Le chemin est incorrect**
- Les accesseurs du modèle convertissent automatiquement en base64
- Vérifie que `$ecole->logo_full_path` retourne une data URI

**Problème 3: L'image est trop grande**
- Réduis la taille de l'image (max 2MB recommandé)
- Utilise un format JPG ou PNG

**Problème 4: DomPDF ne charge pas l'image**
- Les images sont converties en base64 automatiquement
- Vérifie les logs Laravel: `storage/logs/laravel.log`

### Tester manuellement la conversion base64

Crée un fichier `test-base64.php`:
```php
<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ecole = \App\Models\Ecole::find('019b65e9-61d8-7291-b7b1-b208bd625079');

echo "Logo path: " . $ecole->logo_path . "\n";
echo "Logo full path (base64): " . substr($ecole->logo_full_path, 0, 100) . "...\n";
```

Exécute:
```bash
php test-base64.php
```

Tu devrais voir:
```
Logo path: ecoles/logos/xxx.jpg
Logo full path (base64): data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD...
```

---

## Formats d'images supportés

✅ **Supportés:**
- JPG / JPEG
- PNG
- GIF

❌ **Non supportés:**
- SVG (converti en PNG d'abord)
- WebP (converti en PNG d'abord)
- TIFF

---

## Taille recommandée

- **Logo**: 200x200 px à 400x400 px
- **Emblème**: 150x150 px à 300x300 px
- **Poids**: Max 2MB (recommandé < 500KB)

---

## Exemple complet avec Postman

1. **Créer une école:**
```json
POST http://127.0.0.1:8000/api/ecoles
{
    "code_ecole": "TEST",
    "libelle_ecole": "École de Test",
    "region": "CENTRE",
    "localisation": "Yaoundé",
    "est_actif": true
}
```
→ Copie l'ID retourné

2. **Uploader le logo:**
```
POST http://127.0.0.1:8000/api/ecoles/{ID}/upload-file
Body (form-data):
- file: [ton image]
- type: logo
```

3. **Générer le PDF:**
```
GET http://127.0.0.1:8000/api/ecoles/{ID}/preview-header
```

Le logo devrait s'afficher au centre du PDF! 🎉
