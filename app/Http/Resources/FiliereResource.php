<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FiliereResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code_filiere' => $this->code_filiere,
            'libelle_filiere' => $this->libelle_filiere,
            'departement_id' => $this->departement_id,
            'desc_filiere' => $this->desc_filiere,
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            
            'departement' => new DepartementResource($this->whenLoaded('departement')),
            'niveaux' => NiveauResource::collection($this->whenLoaded('niveaux')),
        ];
    }
}
