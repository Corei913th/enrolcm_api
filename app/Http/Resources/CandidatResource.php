<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidatResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'utilisateur_id' => $this->utilisateur_id,
            'nom_complet' => $this->nom_complet,
            'nom_cand' => $this->nom_cand,
            'prenom_cand' => $this->prenom_cand,
            'date_naissance_cand' => $this->date_naissance_cand?->format('Y-m-d'),
            'age_cand' => $this->age_cand,
            'sexe_cand' => $this->sexe_cand,
            'nationalite_cand' => $this->nationalite_cand,
            'adresse_cand' => $this->adresse_cand,
            'telephone_candidat' => $this->telephone_candidat,
            'numero_cni' => $this->numero_cni,
            'niveau_scolaire' => $this->niveau_scolaire,
            'filiere_origine' => $this->filiere_origine,
            'diplome_admission' => $this->diplome_admission,
            'mention' => $this->mention,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            
            // Relations
            'utilisateur' => new UtilisateurResource($this->whenLoaded('utilisateur')),
            'candidatures' => CandidatureResource::collection($this->whenLoaded('candidatures')),
        ];
    }
}
