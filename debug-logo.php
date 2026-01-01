<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DIAGNOSTIC LOGO PDF ===\n\n";

// Récupérer une école
$ecole = \App\Models\Ecole::first();

if (!$ecole) {
    echo "❌ Aucune école trouvée dans la base de données\n";
    exit;
}

echo "✅ École trouvée: {$ecole->libelle_ecole}\n";
echo "   ID: {$ecole->id}\n\n";

// Vérifier logo_path
echo "1. Vérification de logo_path:\n";
echo "   logo_path = " . ($ecole->logo_path ?? 'NULL') . "\n";

if (!$ecole->logo_path) {
    echo "   ❌ Aucun logo uploadé pour cette école\n";
    echo "   → Upload un logo d'abord avec POST /api/ecoles/{$ecole->id}/upload-file\n\n";
    exit;
}

echo "   ✅ logo_path existe\n\n";

// Vérifier le fichier physique
echo "2. Vérification du fichier physique:\n";
$fullPath = storage_path('app/public/' . $ecole->logo_path);
echo "   Chemin complet: $fullPath\n";

if (!file_exists($fullPath)) {
    echo "   ❌ Le fichier n'existe pas!\n";
    echo "   → Le fichier a peut-être été supprimé manuellement\n\n";
    exit;
}

echo "   ✅ Le fichier existe\n";
echo "   Taille: " . filesize($fullPath) . " bytes\n";
echo "   Type MIME: " . mime_content_type($fullPath) . "\n\n";

// Vérifier l'accesseur logo_full_path
echo "3. Vérification de l'accesseur logo_full_path:\n";
try {
    $logoFullPath = $ecole->logo_full_path;
    
    if (!$logoFullPath) {
        echo "   ❌ logo_full_path retourne NULL\n\n";
        exit;
    }
    
    if (strpos($logoFullPath, 'data:image') === 0) {
        echo "   ✅ logo_full_path retourne une data URI base64\n";
        echo "   Début: " . substr($logoFullPath, 0, 50) . "...\n";
        echo "   Longueur: " . strlen($logoFullPath) . " caractères\n\n";
    } else {
        echo "   ⚠️  logo_full_path ne retourne pas une data URI\n";
        echo "   Valeur: " . substr($logoFullPath, 0, 100) . "\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Erreur lors de l'accès à logo_full_path: " . $e->getMessage() . "\n\n";
    exit;
}

// Vérifier le service PDF
echo "4. Test du service PDF:\n";
try {
    $pdfService = new \App\Services\Ecoles\EcolePdfService();
    $header = $pdfService->generateOfficialHeader($ecole);
    
    echo "   ✅ Header généré avec succès\n";
    
    // Vérifier si l'image est dans le HTML
    if (strpos($header, 'data:image') !== false) {
        echo "   ✅ L'image base64 est présente dans le HTML\n";
    } else {
        echo "   ❌ L'image base64 n'est PAS dans le HTML\n";
        echo "   → Problème dans le template Blade\n";
    }
    
    // Sauvegarder le HTML pour inspection
    file_put_contents('debug-header.html', $header);
    echo "   📄 HTML sauvegardé dans: debug-header.html\n\n";
    
} catch (\Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n\n";
    exit;
}

// Test de génération PDF
echo "5. Test de génération PDF:\n";
try {
    $pdf = $pdfService->generateDocument(
        $ecole,
        'TEST LOGO',
        '<p style="text-align: center; margin-top: 50px;">Test d\'affichage du logo</p>'
    );
    
    $pdf->save('debug-test.pdf');
    echo "   ✅ PDF généré avec succès: debug-test.pdf\n";
    echo "   → Ouvre ce fichier pour vérifier si le logo s'affiche\n\n";
    
} catch (\Exception $e) {
    echo "   ❌ Erreur lors de la génération du PDF: " . $e->getMessage() . "\n\n";
}

echo "=== FIN DU DIAGNOSTIC ===\n";
echo "\nRésumé:\n";
echo "- École: {$ecole->libelle_ecole}\n";
echo "- Logo path: {$ecole->logo_path}\n";
echo "- Fichier existe: " . (file_exists($fullPath) ? 'OUI' : 'NON') . "\n";
echo "- Base64 généré: " . ($logoFullPath ? 'OUI' : 'NON') . "\n";
echo "\nFichiers générés:\n";
echo "- debug-header.html (HTML du header)\n";
echo "- debug-test.pdf (PDF de test)\n";
