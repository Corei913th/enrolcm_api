<?php

namespace App\Models;

use App\Enums\StatutSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Session extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'libelle_session',
        'desc_session',
        'est_actif',
        'statut_session',
        'date_ouverture_inscription',
        'date_fermeture_inscription',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'statut_session' => StatutSession::class,
        'date_ouverture_inscription' => 'date',
        'date_fermeture_inscription' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function concours()
    {
        return $this->belongsToMany(Concours::class, 'concours_session')
            ->using(ConcoursSession::class)
            ->withTimestamps();
    }

    public function concoursSessions()
    {
        return $this->hasMany(ConcoursSession::class, 'session_id');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'session_id');
    }

    public function etatsSession()
    {
        return $this->hasMany(EtatConcoursSession::class, 'concours_session_session_id');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeByStatut($query, StatutSession $statut)
    {
        return $query->where('statut_session', $statut->value);
    }

    public function scopeOuvertes($query)
    {
        return $query->where('statut_session', StatutSession::OUVERT->value);
    }

    // Helpers
    public function accepteInscriptions(): bool
    {
        return $this->statut_session?->accepteInscriptions() ?? false;
    }

    public function estActive(): bool
    {
        return $this->statut_session?->estActive() ?? false;
    }

    public function estTerminee(): bool
    {
        return $this->statut_session?->estTerminee() ?? false;
    }

    public function peutEtreInscrit(): bool
    {
        return $this->est_actif && $this->accepteInscriptions();
    }
}
