<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Centre extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'centres';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'libelle_centre',
        'type_centre',
        'ville_centre',
        'capacite',
        'est_actif',
    ];

    protected $casts = [
        'capacite' => 'integer',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    
    public function salles()
    {
        return $this->hasMany(SalleExamen::class, 'centre_id');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeByVille($query, $ville)
    {
        return $query->where('ville_centre', $ville);
    }

    // Helpers
    public function getCapaciteTotale()
    {
        return $this->salles()->sum('capacite');
    }

    public function getNombreSalles()
    {
        return $this->salles()->count();
    }

    public function hasCapaciteDisponible($nombreCandidats)
    {
        return $this->getCapaciteTotale() >= $nombreCandidats;
    }
}
