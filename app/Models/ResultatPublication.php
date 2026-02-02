<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * @mixin IdeHelperResultatPublication
 */
class ResultatPublication extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'resultat_publications';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'concours_id',
        'session_id',
        'date_publication_prevue',
        'date_publication_effective',
        'est_publie',
        'message_candidat',
        'timer_actif',
    ];

    protected $casts = [
        'date_publication_prevue' => 'datetime',
        'date_publication_effective' => 'datetime',
        'est_publie' => 'boolean',
        'timer_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    // Scopes
    public function scopePublies($query)
    {
        return $query->where('est_publie', true);
    }

    public function scopeNonPublies($query)
    {
        return $query->where('est_publie', false);
    }

    public function scopeTimerActif($query)
    {
        return $query->where('timer_actif', true);
    }

    // Helpers
    public function getTempsRestant(): ?int
    {
        if ($this->est_publie || !$this->timer_actif) {
            return null;
        }

        return $this->date_publication_prevue->diffInSeconds(now());
    }

    public function getTempsRestantFormat(): string
    {
        $seconds = $this->getTempsRestant();
        if ($seconds === null) {
            return 'Publié';
        }

        if ($seconds <= 0) {
            return 'Imminent';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 24) {
            $days = floor($hours / 24);
            $hours = $hours % 24;
            return "{$days}j {$hours}h {$minutes}min";
        }

        return "{$hours}h {$minutes}min {$secs}s";
    }

    public function estEnAttente(): bool
    {
        return !$this->est_publie && $this->timer_actif && $this->date_publication_prevue->isFuture();
    }

    public function peutAfficherTimer(): bool
    {
        return $this->timer_actif && !$this->est_publie;
    }
}
