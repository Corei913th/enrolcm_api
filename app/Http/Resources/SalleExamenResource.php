<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalleExamenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'numero_salle' => $this->numero_salle,
            'capacite' => $this->capacite,
            'centre_id' => $this->centre_id,
            'identifiant_complet' => $this->getIdentifiantComplet(),
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'centre' => new CentreResource($this->whenLoaded('centre')),
        ];
    }
}
