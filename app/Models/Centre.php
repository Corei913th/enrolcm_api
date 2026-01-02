<?php

namespace App\Models;

use App\Enums\RegionCameroun;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Centre extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'centres';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'libelle_centre',
        'type_centre',
        'ville_centre',
        'region',
        'departement',
        'arrondissement',
        'capacite',
        'est_actif',
        'responsable_id',
        'region_id'
    ];

    protected $casts = [
        'capacite' => 'integer',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];



    // Relations
    public function salles()
    {
        return $this->hasMany(SalleExamen::class, 'centre_id');
    }

    public function responsable()
    {
        return $this->belongsTo(ResponsableCentre::class, 'responsable_id', 'utilisateur_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'centre_id');
    }

    public function affectations()
    {
        return $this->hasMany(CandidatureSalle::class, 'centre_id');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeByVille($query, $ville)
    {
        return $query->where('ville_centre', $ville);
    }

    public function scopeByRegion($query, $region)
    {
        // Accepte soit un RegionCameroun enum, soit un Region model, soit un region_id
        if ($region instanceof RegionCameroun) {
            return $query->whereHas('region', function ($q) use ($region) {
                $q->where('libelle', $region);
            });
        } elseif ($region instanceof Region) {
            return $query->where('region_id', $region->id);
        } else {
            return $query->where('region_id', $region);
        }
    }

    public function scopeByDepartement($query, $departement)
    {
        return $query->where('departement', $departement);
    }

    public function scopeByArrondissement($query, $arrondissement)
    {
        return $query->where('arrondissement', $arrondissement);
    }

    public function scopeDansLaRegion($query, $region, $departement = null, $arrondissement = null)
    {
        // Accepte soit un RegionCameroun enum, soit un Region model, soit un region_id
        if ($region instanceof RegionCameroun) {
            $query->whereHas('region', function ($q) use ($region) {
                $q->where('libelle', $region);
            });
        } elseif ($region instanceof Region) {
            $query->where('region_id', $region->id);
        } else {
            $query->where('region_id', $region);
        }

        if ($departement) {
            $query->where('departement', $departement);
        }

        if ($arrondissement) {
            $query->where('arrondissement', $arrondissement);
        }

        return $query;
    }

    // Helpers
    public function getCapaciteTotale()
    {
        return $this->salles()->sum('capacite') ?: $this->capacite;
    }

    public function getNombreSalles()
    {
        return $this->salles()->count();
    }

    public function getCapaciteDisponible()
    {
        $capaciteTotale = $this->getCapaciteTotale();
        $placesOccupees = $this->candidatures()
            ->where('statut_candidature', 'VALIDE')
            ->count();

        return max(0, $capaciteTotale - $placesOccupees);
    }

    public function hasCapaciteDisponible($nombreCandidats)
    {
        return $this->getCapaciteDisponible() >= $nombreCandidats;
    }

    // GÉOGRAPHIE
    public function getAdresseComplete()
    {
        $adresse = [];

        if ($this->arrondissement) $adresse[] = $this->arrondissement;
        if ($this->departement) $adresse[] = $this->departement;
        if ($this->region && $this->region->libelle) {
            $adresse[] = $this->region->libelle->label();
        }
        if ($this->ville_centre) $adresse[] = $this->ville_centre;

        return implode(', ', $adresse);
    }

    public function estDansLaRegion($region)
    {
        // Accepte soit un RegionCameroun enum, soit un Region model, soit un region_id
        if (!$this->region) {
            return false;
        }

        if ($region instanceof RegionCameroun) {
            return $this->region->libelle === $region;
        } elseif ($region instanceof Region) {
            return $this->region_id === $region->id;
        } else {
            return $this->region_id === $region;
        }
    }

    // STATISTIQUES
    public function getNombreCandidatsInscrits()
    {
        return $this->candidatures()
            ->where('statut_candidature', 'VALIDE')
            ->count();
    }

    public function getTauxRemplissage()
    {
        $capaciteTotale = $this->getCapaciteTotale();
        if ($capaciteTotale === 0) return 0;

        $placesOccupees = $this->getNombreCandidatsInscrits();
        return round(($placesOccupees / $capaciteTotale) * 100, 2);
    }
}
