<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\EtatCandidature;

class Etat extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'etats';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'libelle_etat',
        'desc_etat',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    
    public function etatCandidatures()
    {
        return $this->hasMany(EtatCandidature::class, 'etat_id');
    }

    // Helpers
    public function getLibelleLabel()
    {
        return EtatCandidature::label($this->libelle_etat);
    }

    public static function getByLibelle($libelle)
    {
        return static::where('libelle_etat', $libelle)->first();
    }
}
