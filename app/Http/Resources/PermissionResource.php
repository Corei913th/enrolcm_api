<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'libelle_permission' => $this->libelle_permission,
            'description' => $this->desc_permission,
        ];
    }
}
