<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UtilisateurResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'type_utilisateur' => $this->type_utilisateur,
            'est_actif' => $this->est_actif,
            'email_verifie' => $this->email_verifie,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            
            // Relations conditionnelles
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'admin' => new AdminResource($this->whenLoaded('admin')),
            'candidat' => new CandidatResource($this->whenLoaded('candidat')),
            'correcteur' => new CorrecteurResource($this->whenLoaded('correcteur')),
            'responsable_centre' => new ResponsableCentreResource($this->whenLoaded('responsableCentre')),
            
            // Attributs calculés
            'is_admin' => $this->isAdmin(),
            'is_candidat' => $this->isCandidat(),
        ];
    }
}
