<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CorrecteurResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'utilisateur_id' => $this->utilisateur_id,
            'specialite' => $this->specialite,
            'matricule_enseignant' => $this->matricule_enseignant,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),   
            'utilisateur' => new UtilisateurResource($this->whenLoaded('utilisateur')),
        ];
    }
}
