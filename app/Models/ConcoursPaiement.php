<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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
        'montant',
        'date_limite',
        'instructions',
        'est_actif',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_limite' => 'date',
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

    // Helpers
    public function isExpire(): bool
    {
        return $this->date_limite < now();
    }

    public function joursRestants(): int
    {
        return max(0, now()->diffInDays($this->date_limite, false));
    }
}
