<?php

namespace App\Models;

use App\Enums\StatutPaiement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Paiement extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'paiements';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'candidat_id',
        'concours_id',
        'candidature_id',
        'reference',
        'montant',
        'preuve_paiement',
        'montant_ocr',
        'date_ocr',
        'banque_ocr',
        'numero_compte_ocr',
        'reference_ocr',
        'ocr_confidence',
        'ocr_raw_data',
        'statut',
        'motif_rejet',
        'validated_at',
        'validated_by',
        'validation_notes',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'montant_ocr' => 'decimal:2',
        'date_ocr' => 'date',
        'ocr_confidence' => 'decimal:2',
        'ocr_raw_data' => 'array',
        'statut' => StatutPaiement::class,
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function candidat()
    {
        return $this->belongsTo(Candidat::class, 'candidat_id', 'utilisateur_id');
    }

    public function concours()
    {
        return $this->belongsTo(Concours::class, 'concours_id');
    }

    public function validatedBy()
    {
        return $this->belongsTo(Utilisateur::class, 'validated_by');
    }

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'candidature_id');
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', StatutPaiement::PENDING);
    }

    public function scopeOcrVerifie($query)
    {
        return $query->where('statut', StatutPaiement::OCR_VERIFIE);
    }

    public function scopeValide($query)
    {
        return $query->where('statut', StatutPaiement::VERIFIED);
    }

    public function scopeRejete($query)
    {
        return $query->where('statut', StatutPaiement::REJECTED);
    }

    public function scopeParConcours($query, string $concoursId)
    {
        return $query->where('concours_id', $concoursId);
    }

    // Helpers
    public function isEnAttente(): bool
    {
        return $this->statut === StatutPaiement::PENDING;
    }

    public function isOcrVerifie(): bool
    {
        return $this->statut === StatutPaiement::OCR_VERIFIE;
    }

    public function isValide(): bool
    {
        return $this->statut === StatutPaiement::VERIFIED;
    }

    public function isRejete(): bool
    {
        return $this->statut === StatutPaiement::REJECTED;
    }

    public function valider(string $userId): void
    {
        $this->update([
            'statut' => StatutPaiement::VERIFIED,
            'validated_at' => now(),
            'validated_by' => $userId,
            'motif_rejet' => null,
        ]);
    }

    public function rejeter(string $motif, string $userId): void
    {
        $this->update([
            'statut' => StatutPaiement::REJECTED,
            'motif_rejet' => $motif,
            'validated_at' => now(),
            'validated_by' => $userId,
        ]);
    }

    public function marquerOcrVerifie(): void
    {
        $this->update(['statut' => StatutPaiement::OCR_VERIFIE]);
    }

    public function hasOcrData(): bool
    {
        return !is_null($this->montant_ocr) || !is_null($this->reference_ocr);
    }

    public function ocrConfidencePercent(): ?int
    {
        return $this->ocr_confidence ? (int)($this->ocr_confidence * 100) : null;
    }
}
