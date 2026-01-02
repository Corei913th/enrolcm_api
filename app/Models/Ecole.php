<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\RegionCameroun;

class Ecole extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code_ecole',
        'libelle_ecole',
        'libelle_ecole_en',
        'nom_directeur',
        'titre_directeur',
        'nom_institution_tutelle',
        'nom_institution_tutelle_en',
        'numero_agrement',
        'date_creation',
        'region',
        'localisation',
        'adresse_complete',
        'ville',
        'logo_url',
        'logo_institution_tutelle_url',
        'bp_ecole',
        'email_ecole',
        'siteweb_ecole',
        'telephone_ecole',
        'fax',
        'telephone_2',
        'devise',
        'slogan',
        'embleme_ecole',
        'mentions_legales',
        'est_actif',
    ];

    protected $casts = [
        'date_creation' => 'date',
        'est_actif' => 'boolean',
        'region' => RegionCameroun::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function departements()
    {
        return $this->hasMany(Departement::class, 'ecole_id');
    }

    public function getRegionLabel()
    {
        return $this->region?->label();
    }

    // Helpers pour génération de documents
    public function getAdresseComplete()
    {
        $parts = array_filter([
            $this->adresse_complete,
            $this->bp_ecole ? "BP: {$this->bp_ecole}" : null,
            $this->ville,
            $this->region?->label(),
        ]);

        return implode(', ', $parts);
    }

    public function getContactsComplets()
    {
        $contacts = [];

        if ($this->telephone_ecole) {
            $contacts[] = "Tél: {$this->telephone_ecole}";
        }

        if ($this->telephone_2) {
            $contacts[] = $this->telephone_2;
        }

        if ($this->fax) {
            $contacts[] = "Fax: {$this->fax}";
        }

        if ($this->email_ecole) {
            $contacts[] = "Email: {$this->email_ecole}";
        }

        if ($this->siteweb_ecole) {
            $contacts[] = "Web: {$this->siteweb_ecole}";
        }

        return implode(' | ', $contacts);
    }
}
