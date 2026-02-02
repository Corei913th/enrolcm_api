<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Tesseract OCR Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour le service OCR Tesseract
    |
    */

  // Chemin vers l'exécutable Tesseract
  'path' => env('TESSERACT_PATH', 'tesseract'),

  // Langue(s) à utiliser pour l'OCR
  // fra = français, eng = anglais
  // Utiliser '+' pour combiner plusieurs langues: 'fra+eng'
  'language' => env('TESSERACT_LANGUAGE', 'fra+eng'),

  // Timeout en secondes pour l'exécution de Tesseract
  'timeout' => env('TESSERACT_TIMEOUT', 30),

  // Mode de segmentation de page (PSM)
  // 0 = Orientation and script detection (OSD) only
  // 1 = Automatic page segmentation with OSD
  // 3 = Fully automatic page segmentation, but no OSD (Default)
  // 6 = Assume a single uniform block of text
  // 11 = Sparse text. Find as much text as possible in no particular order
  'psm' => env('TESSERACT_PSM', 6),

  // Mode du moteur OCR (OEM)
  // 0 = Legacy engine only
  // 1 = Neural nets LSTM engine only
  // 2 = Legacy + LSTM engines
  // 3 = Default, based on what is available
  'oem' => env('TESSERACT_OEM', 3),
];
