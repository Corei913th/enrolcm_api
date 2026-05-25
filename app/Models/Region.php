<?php

namespace App\Models;

use App\Enums\RegionCameroun;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperRegion
 */
class Region extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'regions';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Les champs assignables en masse
     */
    protected $fillable = [
        'code',
        'libelle',
        'est_actif',
    ];

    /**
     * Les types de données pour Eloquent
     */
    protected $casts = [
        'est_actif' => 'boolean',
        'libelle' => RegionCameroun::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];

    public function centres(): HasMany
    {
        return $this->hasMany(Centre::class);
    }
}
