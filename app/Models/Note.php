<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\StatutNote;

class Note extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'notes';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'candidature_id',
        'epreuve_id',
        'valeur',
        'date_saisie',
        'est_definitive',
        'est_eliminatoire',
        'statut',
    ];

    protected $casts = [
        'valeur' => 'decimal:2',
        'date_saisie' => 'datetime',
        'est_definitive' => 'boolean',
        'est_eliminatoire' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    
    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'candidature_id');
    }

    public function epreuve()
    {
        return $this->belongsTo(Epreuve::class, 'epreuve_id', 'id_epreuve');
    }

    // Scopes
    public function scopeDefinitives($query)
    {
        return $query->where('est_definitive', true);
    }

    public function scopeEliminatoires($query)
    {
        return $query->where('est_eliminatoire', true);
    }

    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeEnAttenteSaisie($query)
    {
        return $query->where('statut', StatutNote::EN_ATTENTE_SAISIE);
    }

    // Helpers
    public function getStatutLabel()
    {
        return StatutNote::label($this->statut);
    }

    public function isValide()
    {
        return $this->valeur >= 0 && $this->valeur <= 20;
    }

    public function marquerDefinitive()
    {
        $this->est_definitive = true;
        $this->statut = StatutNote::SAISIE_TERMINEE;
        $this->save();
    }
}
