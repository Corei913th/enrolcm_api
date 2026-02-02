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
            'premiere_langue' => $this->premiere_langue?->value,
            'autre_langue' => $this->autre_langue,
            
            // Informations personnelles
            'date_naissance_cand' => $this->date_naissance_cand?->format('Y-m-d'),
            'lieu_naissance_cand' => $this->lieu_naissance_cand,
            'age_cand' => $this->age_cand,
            'sexe_cand' => $this->sexe_cand,
            'nationalite_cand' => $this->nationalite_cand,
            'numero_cni' => $this->numero_cni,
            'date_delivrance_cni' => $this->date_delivrance_cni?->format('Y-m-d'),
            'statut_matrimonial' => $this->statut_matrimonial?->value,
            'ethnie_cand' => $this->ethnie_cand,
            'handicap' => $this->handicap,
            
            // Contact
            'adresse_cand' => $this->adresse_cand,
            'telephone' => $this->telephone,
            'region' => $this->region?->value,
            'departement' => $this->departement,
            'arrondissement' => $this->arrondissement,
            
            // Famille
            'nom_pere' => $this->nom_pere,
            'telephone_pere' => $this->telephone_pere,
            'nom_parent' => $this->nom_parent,
            'telephone_parent' => $this->telephone_parent,
            'nom_tuteur_cand' => $this->nom_tuteur_cand,
            'telephone_tuteur_cand' => $this->telephone_tuteur_cand,
            
            // Scolarité
            'niveau_scolaire' => $this->niveau_scolaire,
            'filiere_origine' => $this->filiere_origine,
            'etablissement_origine' => $this->etablissement_origine,
            'ville_etablissement' => $this->ville_etablissement,
            'diplome_admission' => $this->diplome_admission,
            'serie_bac' => $this->serie_bac,
            'annee_obtention_bac' => $this->annee_obtention_bac,
            'mention' => $this->mention,
            'annee_diplome' => $this->annee_diplome?->format('Y'),
            
            // Statut
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relations
            'utilisateur' => new UtilisateurResource($this->whenLoaded('utilisateur')),
            'candidatures' => CandidatureResource::collection($this->whenLoaded('candidatures')),
            'filiere' => new FiliereResource($this->whenLoaded('filiere')),
        ];
    }
}
