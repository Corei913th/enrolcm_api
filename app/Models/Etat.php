<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\EtatCandidature;
use App\Models\EtatCandidature as ModelsEtatCandidature;

/**
 * @mixin IdeHelperEtat
 */
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
        return $this->hasMany(ModelsEtatCandidature::class, 'etat_id');
    }

    // Helpers
    public function getLibelleLabel()
    {
        return EtatCandidature::tryFrom($this->libelle_etat)?->label();
    }

    public static function getByLibelle($libelle)
    {
        return static::where('libelle_etat', $libelle)->first();
    }
}
