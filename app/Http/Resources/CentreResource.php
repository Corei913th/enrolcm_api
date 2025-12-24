<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CentreResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'libelle_centre' => $this->libelle_centre,
            'type_centre' => $this->type_centre,
            'ville_centre' => $this->ville_centre,
            'capacite' => $this->capacite,
            'capacite_totale' => $this->getCapaciteTotale(),
            'nombre_salles' => $this->getNombreSalles(),
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            
            'salles' => SalleExamenResource::collection($this->whenLoaded('salles')),
        ];
    }
}
