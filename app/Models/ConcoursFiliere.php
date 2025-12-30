<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ConcoursFiliere extends Pivot
{
    protected $table = 'concours_filiere';
    public $incrementing = false;

    protected $fillable = [
        'concours_id',
        'session_id',
        'filiere_id',
        'nombre_places',
    ];

    protected $casts = [
        'nombre_places' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function concours()
    {
        return $this->belongsTo(Concours::class, 'concours_id');
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'filiere_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    // Helpers
    public function hasPlacesDisponibles()
    {
        return $this->nombre_places > 0;
    }

    public function getNombreCandidatures()
    {
        return Candidature::where('concours_id', $this->concours_id)
            ->where('session_id', $this->session_id)
            ->whereHas('candidat', function ($query) {
                $query->where('filiere_id', $this->filiere_id);
            })
            ->where('statut_candidature', 'VALIDE') // Uniquement les validées
            ->count();
    }

    public function getPlacesRestantes()
    {
        return max(0, $this->nombre_places - $this->getNombreCandidatures());
    }

    public function peutAccepterCandidature()
    {
        return $this->session?->accepteInscriptions() &&
            $this->getPlacesRestantes() > 0;
    }
}
