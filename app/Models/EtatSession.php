<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\EtatSession as EtatSessionEnum;

class EtatSession extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'etat_session';
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

    
    public function getLibelleLabel()
    {
        return EtatSessionEnum::label($this->libelle_etat);
    }

    public function isOpen()
    {
        return $this->libelle_etat === EtatSessionEnum::OUVERTE;
    }

    public function isClosed()
    {
        return $this->libelle_etat === EtatSessionEnum::FERMEE;
    }

    public static function getByLibelle($libelle)
    {
        return static::where('libelle_etat', $libelle)->first();
    }
}
