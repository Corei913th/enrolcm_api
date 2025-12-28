<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SpecConcours extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'specs_concours';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nom_spec',
        'desc_infos_concours',
        'documents_requis',
        'montant_frais_depot',
        'age_minimum',
        'age_maximum',
        'series_bac_acceptees',
        'nationalites_acceptees',
        'est_actif',
    ];

    protected $casts = [
        'documents_requis' => 'array',
        'series_bac_acceptees' => 'array',
        'nationalites_acceptees' => 'array',
        'montant_frais_depot' => 'decimal:2',
        'age_minimum' => 'integer',
        'age_maximum' => 'integer',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function concours()
    {
        return $this->hasMany(Concours::class, 'spec_concours_id');
    }


    // Scopes
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    // Helpers
    public function getDocumentsRequis()
    {
        return $this->documents_requis ?? [];
    }

    public function getNombreDocumentsRequis()
    {
        return count($this->getDocumentsRequis());
    }

    public function hasFraisDepot()
    {
        return $this->montant_frais_depot > 0;
    }

    public function getMontantFormate()
    {
        return number_format($this->montant_frais_depot, 0, ',', ' ') . ' FCFA';
    }

    public function hasAgeRestriction()
    {
        return $this->age_minimum || $this->age_maximum;
    }

    public function isAgeEligible($age)
    {
        if ($this->age_minimum && $age < $this->age_minimum) {
            return false;
        }
        
        if ($this->age_maximum && $age > $this->age_maximum) {
            return false;
        }
        
        return true;
    }

    public function isSerieBacAcceptee($serie)
    {
        if (empty($this->series_bac_acceptees)) {
            return true; // Toutes les séries acceptées si non spécifié
        }
        
        return in_array($serie, $this->series_bac_acceptees);
    }

    public function isNationaliteAcceptee($nationalite)
    {
        if (empty($this->nationalites_acceptees)) {
            return true; // Toutes les nationalités acceptées si non spécifié
        }
        
        return in_array($nationalite, $this->nationalites_acceptees);
    }

    public function getCriteresEligibilite()
    {
        $criteres = [];
        
        if ($this->hasAgeRestriction()) {
            $age = [];
            if ($this->age_minimum) $age[] = "minimum {$this->age_minimum} ans";
            if ($this->age_maximum) $age[] = "maximum {$this->age_maximum} ans";
            $criteres['age'] = implode(', ', $age);
        }
        
        if (!empty($this->series_bac_acceptees)) {
            $criteres['series_bac'] = implode(', ', $this->series_bac_acceptees);
        }
        
        if (!empty($this->nationalites_acceptees)) {
            $criteres['nationalites'] = implode(', ', $this->nationalites_acceptees);
        }
        
        return $criteres;
    }
}
