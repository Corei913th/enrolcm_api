<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Departement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code_departement',
        'libelle_departement',
        'ecole_id',
        'desc_departement',
        'est_actif', 
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ecole()
    {
        return $this->belongsTo(Ecole::class);
    }

    public function filieres()
    {
        return $this->hasMany(Filiere::class, 'departement_id');
    }
}
