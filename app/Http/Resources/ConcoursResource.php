<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConcoursResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'libelle_concours' => $this->libelle_concours,
            'date_limite_depot' => $this->date_limite_depot?->format('Y-m-d'),
            'date_examen' => $this->date_examen?->format('Y-m-d'),
            'nbre_max_places' => $this->nbre_max_places,
            'frais_inscription' => $this->frais_inscription,
            'est_actif' => $this->est_actif,
            'is_ouvert' => $this->isOuvert(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),

            'sessions' => SessionResource::collection($this->whenLoaded('sessions')),
            'candidatures' => CandidatureResource::collection($this->whenLoaded('candidatures')),
        ];
    }
}
