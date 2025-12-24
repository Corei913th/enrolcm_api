<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'est_actif' => 'boolean',
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
}
