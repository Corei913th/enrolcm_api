<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCandidatureSalle
 */
class CandidatureSalle extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'candidature_salle';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'candidature_id',
        'salle_id',
        'planning_epreuve_id',
        'numero_place',
        'est_present',
        'heure_arrivee',
        'observations',
    ];

    protected $casts = [
        'est_present' => 'boolean',
        'heure_arrivee' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'candidature_id');
    }

    public function salle()
    {
        return $this->belongsTo(SalleExamen::class, 'salle_id');
    }

    public function planningEpreuve()
    {
        return $this->belongsTo(PlanningEpreuve::class, 'planning_epreuve_id');
    }

    // Scopes
    public function scopeParSalle($query, $salleId)
    {
        return $query->where('salle_id', $salleId);
    }

    public function scopeParCandidature($query, $candidatureId)
    {
        return $query->where('candidature_id', $candidatureId);
    }

    public function scopePresents($query)
    {
        return $query->where('est_present', true);
    }

    public function scopeAbsents($query)
    {
        return $query->where('est_present', false);
    }

    // Helpers
    public function marquerPresent()
    {
        $this->update([
            'est_present' => true,
            'heure_arrivee' => now(),
        ]);
    }

    public function getStatutPresence()
    {
        return $this->est_present ? 'Présent' : 'Absent';
    }
}
