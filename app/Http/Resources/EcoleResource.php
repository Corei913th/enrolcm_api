<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\RegionCameroun;

class EcoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code_ecole' => $this->code_ecole,
            'libelle_ecole' => $this->libelle_ecole,
            'region' => $this->region,
            'region_label' => $this->region ? RegionCameroun::label($this->region) : null,
            'localisation' => $this->localisation,
            'email_ecole' => $this->email_ecole,
            'telephone_ecole' => $this->telephone_ecole,
            'siteweb_ecole' => $this->siteweb_ecole,
            'devise' => $this->devise,
            'bp_ecole' => $this->bp_ecole,
            'logo_url' => $this->logo_url,
            'embleme_ecole' => $this->embleme_ecole,
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relations
            'departements' => DepartementResource::collection($this->whenLoaded('departements')),
            'departements_count' => $this->when(
                $this->relationLoaded('departements'),
                fn() => $this->departements->count()
            ),
        ];
    }
}
