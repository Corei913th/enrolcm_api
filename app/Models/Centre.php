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
    ];

    protected $casts = [
        'capacite' => 'integer',
        'est_actif' => 'boolean',
        'region' => RegionCameroun::class,
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

    public function scopeByRegion($query, RegionCameroun $region)
    {
        return $query->where('region', $region->value);
    }

    public function scopeByDepartement($query, $departement)
    {
        return $query->where('departement', $departement);
    }

    public function scopeByArrondissement($query, $arrondissement)
    {
        return $query->where('arrondissement', $arrondissement);
    }

    public function scopeDansLaRegion($query, RegionCameroun $region, $departement = null, $arrondissement = null)
    {
        $query->where('region', $region->value);

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
        if ($this->region) $adresse[] = $this->region->label();
        if ($this->ville_centre) $adresse[] = $this->ville_centre;

        return implode(', ', $adresse);
    }

    public function estDansLaRegion(RegionCameroun $region)
    {
        return $this->region === $region;
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
