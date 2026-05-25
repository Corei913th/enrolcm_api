<?php

namespace App\Models;

use App\Enums\EtatSession as EtatSessionEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperEtatSession
 */
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
        return EtatSession::tryFrom($this->libelle_etat)?->label();
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
