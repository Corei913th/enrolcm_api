<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatiereResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code_matiere' => $this->code_matiere,
            'libelle_matiere' => $this->libelle_matiere,
            'coefficient' => $this->coefficient,
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),     
            'niveaux' => NiveauResource::collection($this->whenLoaded('niveaux')),
        ];
    }
}
