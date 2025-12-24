<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartementResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code_departement' => $this->code_departement,
            'libelle_departement' => $this->libelle_departement,
            'ecole_id' => $this->ecole_id,
            'desc_departement' => $this->desc_departement,
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'ecole' => new EcoleResource($this->whenLoaded('ecole')),
            'filieres' => FiliereResource::collection($this->whenLoaded('filieres')),
        ];
    }
}
