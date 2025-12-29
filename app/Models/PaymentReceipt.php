<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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
        return $query->where('statut_verification', 'en_attente');
    }

    public function scopeVerifie($query)
    {
        return $query->where('statut_verification', 'verifie');
    }

    public function scopeRejete($query)
    {
        return $query->where('statut_verification', 'rejete');
    }

    // Helpers
    public function isEnAttente()
    {
        return $this->statut_verification === 'en_attente';
    }

    public function isVerifie()
    {
        return $this->statut_verification === 'verifie';
    }

    public function isRejete()
    {
        return $this->statut_verification === 'rejete';
    }

    public function verifier($userId)
    {
        $this->statut_verification = 'verifie';
        $this->verified_at = now();
        $this->verified_by = $userId;
        $this->motif_rejet = null;
        $this->save();
    }

    public function rejeter($motif, $userId)
    {
        $this->statut_verification = 'rejete';
        $this->motif_rejet = $motif;
        $this->verified_at = now();
        $this->verified_by = $userId;
        $this->save();
    }
}
