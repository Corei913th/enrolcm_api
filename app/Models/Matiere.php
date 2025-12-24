<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Matiere extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code_matiere',
        'libelle_matiere',
        'coefficient',
        'est_actif',
    ];

    protected $casts = [
        'coefficient' => 'integer',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function niveaux()
    {
        return $this->belongsToMany(Niveau::class, 'niveau_matiere');
    }
}
