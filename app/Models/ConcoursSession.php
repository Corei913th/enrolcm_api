<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ConcoursSession extends Pivot
{
    use HasFactory;

    protected $table = 'concours_session';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'concours_id',
        'session_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relations
    public function concours()
    {
        return $this->belongsTo(Concours::class, 'concours_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'concours_id', 'concours_id')
            ->where('session_id', $this->session_id);
    }

    public function etats()
    {
        return $this->hasMany(EtatConcoursSession::class, 'concours_session_concours_id', 'concours_id')
            ->where('concours_session_session_id', $this->session_id);
    }

    // Helpers
    public function getLibelleComplet()
    {
        return "{$this->concours->libelle_concours} - {$this->session->libelle_session}";
    }

    public function getNombreCandidatures()
    {
        return $this->candidatures()->count();
    }

    public function isOuvert()
    {
        return $this->concours->isOuvert() && $this->session->est_actif;
    }
}
