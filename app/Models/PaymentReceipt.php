<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\StatutVerificationPaiement;

class PaymentReceipt extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'payment_receipts';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'candidat_id',
        'numero_recu',
        'banque',
        'montant',
        'date_paiement',
        'image_path',
        'ocr_data',
        'statut_verification',
        'motif_rejet',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
        'ocr_data' => 'array',
        'statut_verification' => StatutVerificationPaiement::class,
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function candidat()
    {
        return $this->belongsTo(Candidat::class, 'candidat_id', 'utilisateur_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(Utilisateur::class, 'verified_by');
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut_verification', StatutVerificationPaiement::EN_ATTENTE);
    }

    public function scopeVerifie($query)
    {
        return $query->where('statut_verification', StatutVerificationPaiement::VERIFIE);
    }

    public function scopeRejete($query)
    {
        return $query->where('statut_verification', StatutVerificationPaiement::REJETE);
    }

    // Helpers
    public function isVerifie(): bool
    {
        return $this->statut_verification === StatutVerificationPaiement::VERIFIE;
    }

    public function isRejete(): bool
    {
        return $this->statut_verification === StatutVerificationPaiement::REJETE;
    }

    public function isEnAttente(): bool
    {
        return $this->statut_verification === StatutVerificationPaiement::EN_ATTENTE;
    }

    public function verify(Utilisateur $verifier): void
    {
        $this->update([
            'statut_verification' => StatutVerificationPaiement::VERIFIE,
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'motif_rejet' => null,
        ]);
    }

    public function reject(string $motif, Utilisateur $verifier): void
    {
        $this->update([
            'statut_verification' => StatutVerificationPaiement::REJETE,
            'motif_rejet' => $motif,
            'verified_at' => now(),
            'verified_by' => $verifier->id,
        ]);
    }
}
