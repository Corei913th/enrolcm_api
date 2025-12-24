<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\DecisionAdmission;
use App\Enums\Mention;

class ResultatFinal extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'resultats_finaux';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'candidature_id',
        'moyenne_generale',
        'total_point',
        'rang',
        'decision',
        'mention',
        'est_admis',
        'date_publication',
    ];

    protected $casts = [
        'moyenne_generale' => 'decimal:2',
        'total_point' => 'decimal:2',
        'rang' => 'integer',
        'est_admis' => 'boolean',
        'date_publication' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    
    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'candidature_id');
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
        return $this->decision ? DecisionAdmission::label($this->decision) : null;
    }

    public function getMentionLabel()
    {
        return $this->mention ? Mention::label($this->mention) : null;
    }

    public function calculerMention()
    {
        if ($this->moyenne_generale >= 16) {
            return Mention::EXCELLENT;
        } elseif ($this->moyenne_generale >= 14) {
            return Mention::TRES_BIEN;
        } elseif ($this->moyenne_generale >= 12) {
            return Mention::BIEN;
        } elseif ($this->moyenne_generale >= 10) {
            return Mention::ASSEZ_BIEN;
        } else {
            return Mention::PASSABLE;
        }
    }

    public function isAdmisDefinitif()
    {
        return $this->decision === DecisionAdmission::ADMIS && $this->est_admis;
    }
}
