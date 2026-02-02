<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AdmissionRule extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'admission_rules';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'concours_id',
        'session_id',
        'seuil_admission_standard',
        'seuil_admission_minimum',
        'permet_admission_conditionnelle',
        'pourcentage_places_conditionnelles',
        'criteres_prioritaires',
        'quotas_regionaux',
        'est_actif',
    ];

    protected $casts = [
        'seuil_admission_standard' => 'decimal:2',
        'seuil_admission_minimum' => 'decimal:2',
        'permet_admission_conditionnelle' => 'boolean',
        'pourcentage_places_conditionnelles' => 'integer',
        'criteres_prioritaires' => 'array',
        'quotas_regionaux' => 'array',
        'est_actif' => 'boolean',
    ];

    // Relations
    public function concours()
    {
        return $this->belongsTo(Concours::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Get default rule if none configured
     */
    public static function getDefault(): self
    {
        $rule = new self();
        $rule->seuil_admission_standard = 12.00;
        $rule->seuil_admission_minimum = 10.00;
        $rule->permet_admission_conditionnelle = true;
        $rule->pourcentage_places_conditionnelles = 15;
        $rule->criteres_prioritaires = ['age', 'region', 'main_subjects'];
        $rule->quotas_regionaux = [];
        $rule->est_actif = true;

        return $rule;
    }
}
