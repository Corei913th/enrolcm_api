<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom_role' => $this->libelle_role,
            'description' => $this->desc_role,
            'est_actif' => $this->est_actif,
            
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
