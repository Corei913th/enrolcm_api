<?php

namespace App\Models;

use App\Enums\CategorieAdmission;
use App\Enums\DecisionAdmission;
use App\Enums\Mention;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperResultatFinal
 */
class ResultatFinal extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'resultats_finaux';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'candidature_id',
        'session_id',
        'moyenne_generale',
        'total_point',
        'rang',
        'decision',
        'mention',
        'est_admis',
        'categorie_admission',
        'score_departage',
        'date_publication',
    ];

    protected $casts = [
        'moyenne_generale' => 'decimal:2',
        'total_point' => 'decimal:2',
        'rang' => 'integer',
        'decision' => DecisionAdmission::class,
        'mention' => Mention::class,
        'est_admis' => 'boolean',
        'categorie_admission' => CategorieAdmission::class,
        'score_departage' => 'decimal:4',
        'date_publication' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'candidature_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    // Scopes
    public function scopeAdmis($query)
    {
        return $query->where('est_admis', true);
    }

    public function scopeByDecision($query, $decision)
    {
        return $query->where('decision', $decision);
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopePublies($query)
    {
        return $query->whereNotNull('date_publication');
    }

    public function scopeTopRangs($query, $limit = 10)
    {
        return $query->whereNotNull('rang')
            ->orderBy('rang', 'asc')
            ->limit($limit);
    }

    // Helpers
    public function getDecisionLabel()
    {
        return $this->decision?->label();
    }

    public function getMentionLabel()
    {
        return $this->mention?->label();
    }

    public function isAdmisDefinitif()
    {
        return $this->decision === DecisionAdmission::ADMIS && $this->est_admis;
    }
}
