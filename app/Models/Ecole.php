<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\RegionCameroun;

class Ecole extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code_ecole',
        'libelle_ecole',
        'region',
        'localisation',
        'logo_url',
        'bp_ecole',
        'email_ecole',
        'siteweb_ecole',
        'devise',
        'telephone_ecole',
        'embleme_ecole',
        'est_actif',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function departements()
    {
        return $this->hasMany(Departement::class, 'ecole_id');
    }

    public function getRegionLabelAttribute()
    {
        return $this->region ? RegionCameroun::label($this->region) : null;
    }
}
