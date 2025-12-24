<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Niveau extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'niveaux';

    protected $fillable = [
        'code_niveau',
        'libelle_niveau',
        'filiere_id',
        'ordre',
        'desc_niveau',
        'est_actif',
    ];

    protected $casts = [
        'ordre' => 'integer',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function filieres()
    {
        return $this->belongsToMany(Filiere::class, 'filiere_niveau');
    }

    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'niveau_matiere');
    }
}
