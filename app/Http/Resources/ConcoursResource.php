<?php

namespace App\Http\Resources;

use App\Services\Domain\Concours\Checkers\ConcoursStatusChecker;
use Illuminate\Http\Resources\Json\JsonResource;

class ConcoursResource extends JsonResource
{
    public function toArray($request)
    {
        $statusChecker = app(ConcoursStatusChecker::class);

        return [
            'id' => $this->id,
            'libelle_concours' => $this->libelle_concours,
            'date_limite_depot' => $this->date_limite_depot?->format('Y-m-d'),
            'date_examen' => $this->date_examen?->format('Y-m-d'),
            'nombre_places' => $this->nbre_max_places,
            'frais_inscription' => $this->frais_inscription,
            'est_actif' => $this->est_actif,
            'is_ouvert' => $statusChecker->isOpen($this->resource),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),

            'filieres' => FiliereResource::collection($this->whenLoaded('filieres')),
            'sessions' => SessionResource::collection($this->whenLoaded('sessions')),
            'candidatures' => CandidatureResource::collection($this->whenLoaded('candidatures')),
        ];
    }
}
