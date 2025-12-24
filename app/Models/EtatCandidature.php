<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class EtatCandidature extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'etat_candidature';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'candidature_id',
        'etat_id',
        'date_etat',
    ];

    protected $casts = [
        'date_etat' => 'datetime',
        'created_at' => 'datetime',
    ];

    
    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'candidature_id');
    }

    public function etat()
    {
        return $this->belongsTo(Etat::class, 'etat_id');
    }

    // Scopes
    public function scopeRecent($query)
    {
        return $query->orderBy('date_etat', 'desc');
    }

    public function scopeByCandidature($query, $candidatureId)
    {
        return $query->where('candidature_id', $candidatureId);
    }

    // Helpers
    public function getLibelleEtat()
    {
        return $this->etat ? $this->etat->getLibelleLabel() : null;
    }
}
