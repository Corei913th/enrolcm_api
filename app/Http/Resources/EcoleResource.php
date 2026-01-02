<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\RegionCameroun;

class EcoleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code_ecole' => $this->code_ecole,
            'libelle_ecole' => $this->libelle_ecole,
            'libelle_ecole_en' => $this->libelle_ecole_en,
            'region' => $this->region,
            'region_label' => $this->region?->label(),
            'localisation' => $this->localisation,
            'adresse_complete' => $this->adresse_complete,
            'ville' => $this->ville,
            'telephone_ecole' => $this->telephone_ecole,
            'fax' => $this->fax,
            'telephone_2' => $this->telephone_2,
            'email_ecole' => $this->email_ecole,
            'siteweb_ecole' => $this->siteweb_ecole,
            'bp_ecole' => $this->bp_ecole,
            'logo_url' => $this->logo_url,
            'logo_institution_tutelle_url' => $this->logo_institution_tutelle_url,
            'embleme_ecole' => $this->embleme_ecole,
            'nom_directeur' => $this->nom_directeur,
            'titre_directeur' => $this->titre_directeur,
            'nom_institution_tutelle' => $this->nom_institution_tutelle,
            'nom_institution_tutelle_en' => $this->nom_institution_tutelle_en,
            'numero_agrement' => $this->numero_agrement,
            'date_creation' => $this->date_creation?->format('Y-m-d'),
            'devise' => $this->devise,
            'slogan' => $this->slogan,
            'mentions_legales' => $this->mentions_legales,
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'departements' => DepartementResource::collection($this->whenLoaded('departements')),
        ];
    }
}
