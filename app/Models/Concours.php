<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Concours extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'concours';

    protected $fillable = [
        'libelle_concours',
        'date_limite_depot',
        'date_examen',
        'nbre_max_places',
        'frais_inscription',
        'est_actif',
    ];

    protected $casts = [
        'date_limite_depot' => 'date',
        'date_examen' => 'date',
        'nbre_max_places' => 'integer',
        'frais_inscription' => 'decimal:2',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'concours_session')
            ->using(ConcoursSession::class)
            ->withTimestamps();
    }

    public function concoursSessions()
    {
        return $this->hasMany(ConcoursSession::class, 'concours_id');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'concours_id');
    }

    public function etatsConcours()
    {
        return $this->hasMany(EtatConcoursSession::class, 'concours_session_concours_id');
    }

    public function isOuvert(): bool
    {
        return $this->est_actif && now()->lte($this->date_limite_depot);
    }
}
