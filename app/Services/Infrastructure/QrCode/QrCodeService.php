<?php

namespace App\Services\Infrastructure\QrCode;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate QR code and return as base64
     * Using SVG format to avoid imagick dependency
     */
    public function generateBase64(string $data, int $size = 200): string
    {
        try {
            $svg = QrCode::format('svg')
                ->size($size)
                ->errorCorrection('H')
                ->generate($data);

            return base64_encode($svg);
        } catch (\Exception $e) {
            // Fallback: generate simple data URI if QR generation fails
            \Log::warning('QR code generation failed, using fallback', [
                'error' => $e->getMessage(),
                'data_length' => strlen($data),
            ]);

            // Return a simple placeholder base64 string
            return base64_encode('QR_CODE_PLACEHOLDER_' . md5($data));
        }
    }

    /**
     * Generate QR code and save to storage
     */
    public function generateAndSave(string $data, string $filename, int $size = 200): string
    {
        $qrCode = QrCode::format('png')
            ->size($size)
            ->errorCorrection('H')
            ->generate($data);

        $path = 'qrcodes/' . $filename;
        Storage::disk('public')->put($path, $qrCode);

        return $path;
    }

    /**
     * Generate QR code for candidature
     */
    public function generateForCandidature(object $candidature, int $size = 200): string
    {
        $data = json_encode([
            'type' => 'candidature',
            'numero' => $candidature->numero_candidature,
            'id' => $candidature->id,
            'concours_id' => $candidature->concours_id,
            'candidat_id' => $candidature->candidat_id,
        ]);

        return $this->generateBase64($data, $size);
    }

    /**
     * Generate QR code for convocation
     */
    public function generateForConvocation(object $candidature, int $size = 200): string
    {
        $url = route('candidat.convocation.verify', [
            'numero' => $candidature->numero_candidature,
            'token' => $candidature->verification_token ?? md5($candidature->id),
        ]);

        return $this->generateBase64($url, $size);
    }

    /**
     * Generate QR code for payment verification
     */
    public function generateForPaiement(object $paiement, int $size = 200): string
    {
        $data = json_encode([
            'type' => 'paiement',
            'transaction_id' => $paiement->transaction_id,
            'montant' => $paiement->montant,
            'statut' => $paiement->statut,
            'date' => $paiement->created_at->format('Y-m-d H:i:s'),
        ]);

        return $this->generateBase64($data, $size);
    }

    /**
     * Generate QR code for attendance check
     */
    public function generateForPresence(object $candidature, object $epreuve, int $size = 200): string
    {
        $data = json_encode([
            'type' => 'presence',
            'candidature_id' => $candidature->id,
            'epreuve_id' => $epreuve->id_epreuve,
            'numero' => $candidature->numero_candidature,
            'timestamp' => now()->timestamp,
        ]);

        return $this->generateBase64($data, $size);
    }

    /**
     * Generate QR code for document verification
     */
    public function generateForDocument(string $documentId, string $type, int $size = 200): string
    {
        $data = json_encode([
            'type' => 'document',
            'document_id' => $documentId,
            'document_type' => $type,
            'verification_url' => route('document.verify', $documentId),
        ]);

        return $this->generateBase64($data, $size);
    }

    /**
     * Generate QR code with logo
     */
    public function generateWithLogo(string $data, string $logoPath, int $size = 200): string
    {
        return base64_encode(
            QrCode::format('png')
                ->size($size)
                ->errorCorrection('H')
                ->merge($logoPath, 0.3, true)
                ->generate($data)
        );
    }

    /**
     * Decode QR code data
     */
    public function decode(string $imagePath): ?array
    {
        try {
            // This requires zxing or similar library
            // For now, return null as placeholder
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
