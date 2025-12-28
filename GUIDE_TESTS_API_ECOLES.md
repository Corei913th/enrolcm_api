# Guide de Tests API Écoles

## Configuration
- **Base URL**: `http://127.0.0.1:8000/api`
- **Authentification**: Désactivée temporairement pour les tests

## 1. Lister toutes les écoles

```bash
curl -X GET "http://127.0.0.1:8000/api/ecoles" -H "Accept: application/json"
```

**Avec filtres:**
```bash
curl -X GET "http://127.0.0.1:8000/api/ecoles?region=CENTRE&est_actif=1&page=1&per_page=10" -H "Accept: application/json"
```

## 2. Lister les écoles actives uniquement

```bash
curl -X GET "http://127.0.0.1:8000/api/ecoles/actives" -H "Accept: application/json"
```

## 3. Créer une nouvelle école

```bash
curl -X POST "http://127.0.0.1:8000/api/ecoles" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "code_ecole": "ESSTIC",
    "libelle_ecole": "École Supérieure des Sciences et Techniques de l'\''Information",
    "region": "CENTRE",
    "localisation": "Yaoundé",
    "email_ecole": "contact@esstic.cm",
    "telephone_ecole": "+237222789012",
    "siteweb_ecole": "https://esstic.cm",
    "devise": "Communiquer pour Innover",
    "bp_ecole": "BP 1234",
    "est_actif": true
  }'
```

**Réponse attendue:**
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

## 4. Voir une école par ID

```bash
# Remplacer {ID} par l'ID de l'école
curl -X GET "http://127.0.0.1:8000/api/ecoles/{ID}" -H "Accept: application/json"
```

**Exemple:**
```bash
curl -X GET "http://127.0.0.1:8000/api/ecoles/019b65e9-61b8-72f0-bad3-077d054ab5f2" -H "Accept: application/json"
```

## 5. Voir une école par code

```bash
curl -X GET "http://127.0.0.1:8000/api/ecoles/code/ENSP" -H "Accept: application/json"
```

## 6. Modifier une école

```bash
# Remplacer {ID} par l'ID de l'école
curl -X PUT "http://127.0.0.1:8000/api/ecoles/{ID}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "libelle_ecole": "École Supérieure - Mise à jour",
    "email_ecole": "nouveau@email.cm",
    "telephone_ecole": "+237222999999"
  }'
```

## 7. Activer/Désactiver une école

```bash
# Remplacer {ID} par l'ID de l'école
curl -X PATCH "http://127.0.0.1:8000/api/ecoles/{ID}/toggle-status" \
  -H "Accept: application/json"
```

## 8. Upload logo école

```bash
# Remplacer {ID} par l'ID de l'école et /path/to/logo.png par le chemin du fichier
curl -X POST "http://127.0.0.1:8000/api/ecoles/{ID}/upload-file" \
  -H "Accept: application/json" \
  -F "file=@/path/to/logo.png" \
  -F "type=logo"
```

**Formats acceptés:** PNG, JPG, JPEG  
**Taille max:** 2MB

## 9. Upload emblème école

```bash
# Remplacer {ID} par l'ID de l'école
curl -X POST "http://127.0.0.1:8000/api/ecoles/{ID}/upload-file" \
  -H "Accept: application/json" \
  -F "file=@/path/to/embleme.png" \
  -F "type=embleme"
```

## 10. Supprimer logo école

```bash
# Remplacer {ID} par l'ID de l'école
curl -X DELETE "http://127.0.0.1:8000/api/ecoles/{ID}/delete-file" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"type": "logo"}'
```

## 11. Supprimer emblème école

```bash
# Remplacer {ID} par l'ID de l'école
curl -X DELETE "http://127.0.0.1:8000/api/ecoles/{ID}/delete-file" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"type": "embleme"}'
```

## 12. Générer attestation PDF

```bash
# Remplacer {ID} par l'ID de l'école
curl -X POST "http://127.0.0.1:8000/api/ecoles/{ID}/generate-attestation" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "candidat_nom": "KAMGA",
    "candidat_prenom": "Jean Pierre",
    "numero_candidat": "CAND2024001",
    "concours": "Concours d'\''entrée 2024",
    "date_concours": "2024-06-15"
  }' \
  --output attestation.pdf
```

## 13. Prévisualiser en-tête PDF

```bash
# Remplacer {ID} par l'ID de l'école
# Ouvrir dans le navigateur:
http://127.0.0.1:8000/api/ecoles/{ID}/preview-header
```

## 14. Supprimer une école

```bash
# Remplacer {ID} par l'ID de l'école
curl -X DELETE "http://127.0.0.1:8000/api/ecoles/{ID}" \
  -H "Accept: application/json"
```

---

## Tests avec PowerShell (Windows)

### 1. Lister les écoles
```powershell
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles" -Method GET -Headers @{"Accept"="application/json"} -UseBasicParsing
$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10
```

### 2. Créer une école
```powershell
$body = @{
    code_ecole = "ESSTIC"
    libelle_ecole = "École Supérieure des Sciences et Techniques"
    region = "CENTRE"
    localisation = "Yaoundé"
    email_ecole = "contact@esstic.cm"
    telephone_ecole = "+237222789012"
    est_actif = $true
} | ConvertTo-Json

$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles" `
    -Method POST `
    -Headers @{"Accept"="application/json"; "Content-Type"="application/json"} `
    -Body $body `
    -UseBasicParsing

$result = $response.Content | ConvertFrom-Json
$ecoleId = $result.data.id
Write-Host "École créée avec ID: $ecoleId"
```

### 3. Voir une école
```powershell
$ecoleId = "019b65e9-61b8-72f0-bad3-077d054ab5f2"
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles/$ecoleId" -Method GET -Headers @{"Accept"="application/json"} -UseBasicParsing
$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10
```

### 4. Modifier une école
```powershell
$ecoleId = "019b65e9-61b8-72f0-bad3-077d054ab5f2"
$body = @{
    libelle_ecole = "École Nationale Supérieure Polytechnique - Mise à jour"
    email_ecole = "nouveau@ensp.cm"
} | ConvertTo-Json

$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles/$ecoleId" `
    -Method PUT `
    -Headers @{"Accept"="application/json"; "Content-Type"="application/json"} `
    -Body $body `
    -UseBasicParsing

$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10
```

### 5. Toggle statut
```powershell
$ecoleId = "019b65e9-61b8-72f0-bad3-077d054ab5f2"
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles/$ecoleId/toggle-status" `
    -Method PATCH `
    -Headers @{"Accept"="application/json"} `
    -UseBasicParsing

$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10
```

### 6. Supprimer une école
```powershell
$ecoleId = "019b65e9-6209-739c-8922-ead09e36a04c"
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles/$ecoleId" `
    -Method DELETE `
    -Headers @{"Accept"="application/json"} `
    -UseBasicParsing

$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10
```

---

## Scénario de test complet

```powershell
# 1. Lister les écoles existantes
Write-Host "=== 1. Liste des écoles ===" -ForegroundColor Green
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles" -Method GET -Headers @{"Accept"="application/json"} -UseBasicParsing
$ecoles = ($response.Content | ConvertFrom-Json).data
Write-Host "Nombre d'écoles: $($ecoles.Count)"

# 2. Créer une nouvelle école
Write-Host "`n=== 2. Création d'une école ===" -ForegroundColor Green
$body = @{
    code_ecole = "TEST2024"
    libelle_ecole = "École de Test 2024"
    region = "CENTRE"
    localisation = "Yaoundé"
    email_ecole = "test@ecole.cm"
    telephone_ecole = "+237222000000"
    devise = "Test et Innovation"
    bp_ecole = "BP 9999"
    est_actif = $true
} | ConvertTo-Json

$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles" `
    -Method POST `
    -Headers @{"Accept"="application/json"; "Content-Type"="application/json"} `
    -Body $body `
    -UseBasicParsing

$result = $response.Content | ConvertFrom-Json
$ecoleId = $result.data.id
Write-Host "École créée avec ID: $ecoleId"

# 3. Voir l'école créée
Write-Host "`n=== 3. Détails de l'école ===" -ForegroundColor Green
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles/$ecoleId" -Method GET -Headers @{"Accept"="application/json"} -UseBasicParsing
$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10

# 4. Modifier l'école
Write-Host "`n=== 4. Modification de l'école ===" -ForegroundColor Green
$body = @{
    libelle_ecole = "École de Test 2024 - Modifiée"
    email_ecole = "nouveau@test.cm"
} | ConvertTo-Json

$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles/$ecoleId" `
    -Method PUT `
    -Headers @{"Accept"="application/json"; "Content-Type"="application/json"} `
    -Body $body `
    -UseBasicParsing

Write-Host "École modifiée avec succès"

# 5. Toggle statut
Write-Host "`n=== 5. Désactivation de l'école ===" -ForegroundColor Green
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles/$ecoleId/toggle-status" `
    -Method PATCH `
    -Headers @{"Accept"="application/json"} `
    -UseBasicParsing

$result = $response.Content | ConvertFrom-Json
Write-Host "Nouveau statut: $($result.data.est_actif)"

# 6. Supprimer l'école
Write-Host "`n=== 6. Suppression de l'école ===" -ForegroundColor Green
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/ecoles/$ecoleId" `
    -Method DELETE `
    -Headers @{"Accept"="application/json"} `
    -UseBasicParsing

Write-Host "École supprimée avec succès"

Write-Host "`n=== Tests terminés ===" -ForegroundColor Green
```

## Notes importantes

1. **Middleware désactivé**: L'authentification Sanctum est temporairement désactivée pour faciliter les tests
2. **IDs des écoles**: Utilise les IDs retournés par l'API ou récupère-les via GET /api/ecoles
3. **Validation**: Tous les champs requis doivent être fournis lors de la création
4. **Fichiers**: Les uploads de fichiers nécessitent le format multipart/form-data
5. **Régions valides**: CENTRE, LITTORAL, OUEST, SUD, EST, NORD, ADAMAOUA, NORD_OUEST, SUD_OUEST, EXTREME_NORD
