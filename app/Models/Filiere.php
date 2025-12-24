<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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

    public function niveaux()
    {
        return $this->hasMany(Niveau::class, 'filiere_id');
    }

    public function niveauxPivot()
    {
        return $this->belongsToMany(Niveau::class, 'filiere_niveau');
    }
}
