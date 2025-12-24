<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ConcoursResource;


class SessionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'libelle_session' => $this->libelle_session,
            'desc_session' => $this->desc_session,
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),          
            'concours' => ConcoursResource::collection($this->whenLoaded('concours')),
        ];
    }
}
