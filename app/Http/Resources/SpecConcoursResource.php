<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SpecConcoursResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom_spec' => $this->nom_spec,
            'desc_infos_concours' => $this->desc_infos_concours,
            'documents_requis' => $this->documents_requis,
            'montant_frais_depot' => $this->montant_frais_depot,
            'age_minimum' => $this->age_minimum,
            'age_maximum' => $this->age_maximum,
            'series_bac_acceptees' => $this->series_bac_acceptees,
            'nationalites_acceptees' => $this->nationalites_acceptees,
            'diplomes_requis' => $this->diplomes_requis,
            'criteres_admission_supplementaires' => $this->criteres_admission_supplementaires,
            'accepte_diplomes_equivalents' => $this->accepte_diplomes_equivalents,
            'accepte_candidats_en_cours' => $this->accepte_candidats_en_cours,
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
