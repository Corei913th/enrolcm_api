# Script de test pour uploader un logo et générer un PDF

# Configuration
$baseUrl = "http://127.0.0.1:8000/api"
$ecoleId = "019b65e9-61d8-7291-b7b1-b208bd625079"  # Remplace par ton ID
$logoPath = "C:/Users/adolp/Desktop/logo.jpeg"     # Remplace par le chemin de ton image

Write-Host "=== Test Upload Logo et Génération PDF ===" -ForegroundColor Green

# Étape 1: Vérifier que l'école existe
Write-Host "`n1. Vérification de l'école..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "$baseUrl/ecoles/$ecoleId" -Method GET -Headers @{"Accept"="application/json"} -UseBasicParsing
    $ecole = ($response.Content | ConvertFrom-Json).data
    Write-Host "   École trouvée: $($ecole.libelle_ecole)" -ForegroundColor Green
} catch {
    Write-Host "   Erreur: École non trouvée!" -ForegroundColor Red
    exit
}

# Étape 2: Vérifier que le fichier logo existe
Write-Host "`n2. Vérification du fichier logo..." -ForegroundColor Yellow
if (Test-Path $logoPath) {
    Write-Host "   Fichier trouvé: $logoPath" -ForegroundColor Green
} else {
    Write-Host "   Erreur: Fichier non trouvé: $logoPath" -ForegroundColor Red
    Write-Host "   Modifie la variable `$logoPath dans le script" -ForegroundColor Yellow
    exit
}

# Étape 3: Upload du logo
Write-Host "`n3. Upload du logo..." -ForegroundColor Yellow
try {
    $boundary = [System.Guid]::NewGuid().ToString()
    $LF = "`r`n"
    
    $fileBytes = [System.IO.File]::ReadAllBytes($logoPath)
    $fileEnc = [System.Text.Encoding]::GetEncoding('iso-8859-1').GetString($fileBytes)
    
    $bodyLines = (
        "--$boundary",
        "Content-Disposition: form-data; name=`"file`"; filename=`"logo.jpg`"",
        "Content-Type: image/jpeg$LF",
        $fileEnc,
        "--$boundary",
        "Content-Disposition: form-data; name=`"type`"$LF",
        "logo",
        "--$boundary--$LF"
    ) -join $LF
    
    $response = Invoke-WebRequest `
        -Uri "$baseUrl/ecoles/$ecoleId/upload-file" `
        -Method POST `
        -ContentType "multipart/form-data; boundary=$boundary" `
        -Body $bodyLines `
        -Headers @{"Accept"="application/json"} `
        -UseBasicParsing
    
    $result = $response.Content | ConvertFrom-Json
    Write-Host "   Logo uploadé avec succès!" -ForegroundColor Green
    Write-Host "   Chemin: $($result.data.logo_path)" -ForegroundColor Cyan
} catch {
    Write-Host "   Erreur lors de l'upload: $($_.Exception.Message)" -ForegroundColor Red
    exit
}

# Étape 4: Générer le PDF de prévisualisation
Write-Host "`n4. Génération du PDF..." -ForegroundColor Yellow
try {
    $pdfPath = "preview-header-$ecoleId.pdf"
    Invoke-WebRequest `
        -Uri "$baseUrl/ecoles/$ecoleId/preview-header" `
        -Method GET `
        -Headers @{"Accept"="application/json"} `
        -OutFile $pdfPath `
        -UseBasicParsing
    
    Write-Host "   PDF généré avec succès: $pdfPath" -ForegroundColor Green
    
    # Ouvrir le PDF
    Start-Process $pdfPath
    Write-Host "   PDF ouvert dans le navigateur" -ForegroundColor Green
} catch {
    Write-Host "   Erreur lors de la génération du PDF: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n=== Test terminé ===" -ForegroundColor Green
