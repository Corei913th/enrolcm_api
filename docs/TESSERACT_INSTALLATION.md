# Installation de Tesseract OCR

## ⚠️ IMPORTANT

Le package PHP `thiagoalessio/tesseract_ocr` a été installé via Composer, mais vous devez également installer le **binaire Tesseract** sur votre système pour que l'OCR fonctionne.

## Étapes d'installation

### 1. Installer le package PHP (✅ Déjà fait)

```bash
composer require thiagoalessio/tesseract_ocr
```

### 2. Installer le binaire Tesseract sur votre système

## Windows

1. Télécharger l'installateur depuis : https://github.com/UB-Mannheim/tesseract/wiki
2. Installer Tesseract (par défaut dans `C:\Program Files\Tesseract-OCR`)
3. Ajouter au PATH système : `C:\Program Files\Tesseract-OCR`
4. Télécharger les langues françaises :
   - Aller dans le dossier `tessdata` : `C:\Program Files\Tesseract-OCR\tessdata`
   - Télécharger `fra.traineddata` depuis : https://github.com/tesseract-ocr/tessdata
   - Copier le fichier dans le dossier `tessdata`

## Linux (Ubuntu/Debian)

```bash
sudo apt update
sudo apt install tesseract-ocr
sudo apt install tesseract-ocr-fra
```

## macOS

```bash
brew install tesseract
brew install tesseract-lang
```

## Vérification de l'installation

```bash
tesseract --version
tesseract --list-langs
```

Vous devriez voir `fra` et `eng` dans la liste des langues.

## Installation du package PHP

```bash
composer require thiagoalessio/tesseract_ocr
```

## Configuration Laravel

Ajouter dans `.env` :

```env
TESSERACT_PATH=/usr/bin/tesseract  # Linux/Mac
# ou
TESSERACT_PATH=C:\Program Files\Tesseract-OCR\tesseract.exe  # Windows
```

## Test rapide

```php
use thiagoalessio\TesseractOCR\TesseractOCR;

$text = (new TesseractOCR('image.jpg'))
    ->lang('fra', 'eng')
    ->run();

echo $text;
```
