<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NiveauResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code_niveau' => $this->code_niveau,
            'libelle_niveau' => $this->libelle_niveau,
            'filiere_id' => $this->filiere_id,
            'ordre' => $this->ordre,
            'desc_niveau' => $this->desc_niveau,
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),     
            'filiere' => new FiliereResource($this->whenLoaded('filiere')),
            'matieres' => MatiereResource::collection($this->whenLoaded('matieres')),
        ];
    }
}
