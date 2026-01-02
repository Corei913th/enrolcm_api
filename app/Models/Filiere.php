<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code_filiere',
        'libelle_filiere',
        'departement_id',
        'desc_filiere',
        'est_actif',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }


    public function concours()
    {
        return $this->belongsToMany(Concours::class, 'concours_filiere')
            ->using(ConcoursFiliere::class)
            ->withPivot('nombre_places')
            ->withTimestamps();
    }

    public function niveaux(): HasMany
    {
        return $this->hasMany(Niveau::class);
    }
}
