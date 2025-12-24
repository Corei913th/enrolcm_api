<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResponsableCentreResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'utilisateur_id' => $this->utilisateur_id,
            'code_agent' => $this->code_agent,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),  
            'utilisateur' => new UtilisateurResource($this->whenLoaded('utilisateur')),
        ];
    }
}
