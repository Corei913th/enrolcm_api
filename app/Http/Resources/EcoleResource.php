<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\RegionCameroun;

class EcoleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code_ecole' => $this->code_ecole,
            'libelle_ecole' => $this->libelle_ecole,
            'region' => $this->region,
            'region_label' => RegionCameroun::label($this->region),
            'ville' => $this->ville,
            'adresse' => $this->adresse,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'departements' => DepartementResource::collection($this->whenLoaded('departements')),
        ];
    }
}
