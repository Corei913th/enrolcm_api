<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperMatiere
 */
class Matiere extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code_matiere',
        'niveau_id',
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

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }
}
