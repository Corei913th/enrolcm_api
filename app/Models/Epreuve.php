<?php

namespace App\Models;

use App\Enums\TypeEpreuve;
use App\Traits\HasAdvancedSearch;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperEpreuve
 */
class Epreuve extends Model
{
    use HasAdvancedSearch, HasFactory, HasUuids;

    protected $table = 'epreuves';

    protected $primaryKey = 'id_epreuve';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'intitule',
        'session',
        'url_epreuve',
        'type_epreuve',
        'duree_en_minute',
        'coefficient_defaut',
        'note_eliminatoire',
        'est_eliminatoire',
        'est_actif',
    ];

    protected $casts = [
        'type_epreuve' => TypeEpreuve::class,
        'duree_en_minute' => 'integer',
        'coefficient_defaut' => 'integer',
        'note_eliminatoire' => 'decimal:2',
        'est_eliminatoire' => 'boolean',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function notes()
    {
        return $this->hasMany(Note::class, 'epreuve_id', 'id_epreuve');
    }

    public function plannings()
    {
        return $this->hasMany(PlanningEpreuve::class, 'epreuve_id', 'id_epreuve');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type_epreuve', $type);
    }

    public function scopeBySession($query, $session)
    {
        return $query->where('session', $session);
    }

    public function getDureeFormatee()
    {
        $heures = floor($this->duree_en_minute / 60);
        $minutes = $this->duree_en_minute % 60;

        if ($heures > 0 && $minutes > 0) {
            return "{$heures}h {$minutes}min";
        } elseif ($heures > 0) {
            return "{$heures}h";
        } else {
            return "{$minutes}min";
        }
    }
}
