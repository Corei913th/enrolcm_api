<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'libelle' => $this->libelle?->label() ?? $this->libelle,
            'est_actif' => $this->est_actif,
        ];
    }
}
