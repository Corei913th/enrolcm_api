<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SalleExamen extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'salles_examen';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'numero_salle',
        'capacite',
        'centre_id',
        'est_actif',
    ];

    protected $casts = [
        'capacite' => 'integer',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    
    public function centre()
    {
        return $this->belongsTo(Centre::class, 'centre_id');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeByCentre($query, $centreId)
    {
        return $query->where('centre_id', $centreId);
    }

    // Helpers
    public function getIdentifiantComplet()
    {
        return $this->centre ? 
            "{$this->centre->libelle_centre} - Salle {$this->numero_salle}" : 
            "Salle {$this->numero_salle}";
    }
}
