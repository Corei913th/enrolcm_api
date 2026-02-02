<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * @mixin IdeHelperConcoursPaiement
 */
class ConcoursPaiement extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'concours_paiements';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'concours_id',
        'banque_nom',
        'numero_compte',
        'nom_beneficiaire',
        'devise',
        'code_banque',
        'agence_banque',
        'iban',
        'type_paiement',
        'banques_acceptees',
        'frais_paiement',
        'montant',
        'date_limite',
        'reference_format',
        'minimum_confiance_ocr',
        'validation_auto',
        'instructions',
        'commentaires',
        'date_derniere_modification',
        'est_actif',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'frais_paiement' => 'decimal:2',
        'minimum_confiance_ocr' => 'decimal:2',
        'date_limite' => 'date',
        'date_derniere_modification' => 'datetime',
        'banques_acceptees' => 'array',
        'validation_auto' => 'boolean',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function concours()
    {
        return $this->belongsTo(Concours::class, 'concours_id');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeNonExpire($query)
    {
        return $query->where('date_limite', '>=', now());
    }

    public function scopeParTypePaiement($query, string $type)
    {
        return $query->where('type_paiement', $type);
    }

    public function scopeParDevise($query, string $devise)
    {
        return $query->where('devise', $devise);
    }

    public function scopeValidationAuto($query)
    {
        return $query->where('validation_auto', true);
    }

    public function scopeBanqueAcceptee($query, string $banque)
    {
        return $query->whereJsonContains('banques_acceptees', $banque);
    }

    // Helpers
    public function isExpire(): bool
    {
        return $this->date_limite < now();
    }

    public function joursRestants(): int
    {
        return max(0, now()->diffInDays($this->date_limite, false));
    }

    public function montantTotal(): float
    {
        return $this->montant + $this->frais_paiement;
    }

    public function banqueEstAcceptee(string $nomBanque): bool
    {
        return $this->banques_acceptees && in_array($nomBanque, $this->banques_acceptees);
    }

    public function peutValiderAutomatiquement(): bool
    {
        return $this->validation_auto && $this->est_actif && !$this->isExpire();
    }

    public function getInformationsBancaires(): array
    {
        return [
            'banque' => $this->banque_nom,
            'code_banque' => $this->code_banque,
            'agence' => $this->agence_banque,
            'numero_compte' => $this->numero_compte,
            'iban' => $this->iban,
            'beneficiaire' => $this->nom_beneficiaire,
            'devise' => $this->devise,
        ];
    }
}
