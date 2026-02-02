<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidatureResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'candidat_id' => $this->candidat_id,
            'concours_id' => $this->concours_id,
            'session_id' => $this->session_id,
            'code_cand_temp' => $this->code_cand_temp,
            'code_cand_def' => $this->code_cand_def,
            'statut_inscription' => $this->statut_inscription,
            'statut_candidature' => $this->statut_candidature,
            'date_candidature' => $this->date_candidature?->format('Y-m-d H:i:s'),
            'date_inscription' => $this->date_inscription?->format('Y-m-d'),
            'date_depot_physique' => $this->date_depot_physique?->format('Y-m-d'),
            'date_validation' => $this->date_validation?->format('Y-m-d H:i:s'),
            'motif_rejet' => $this->motif_rejet,

            // Méthodes helpers
            'is_validee' => $this->isValidee(),
            'is_rejetee' => $this->isRejetee(),

            // Eligibility details - calculated dynamically
            'paiement_valide' => $this->hasPaiementValide(),
            'documents_complets' => $this->hasDocumentsComplets(),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relations
            'candidat' => new CandidatResource($this->whenLoaded('candidat')),
            'concours' => new ConcoursResource($this->whenLoaded('concours')),
            'session' => new SessionResource($this->whenLoaded('session')),
            'paiement' => new PaiementResource($this->whenLoaded('paiement')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'notes' => NoteResource::collection($this->whenLoaded('notes')),
            'resultat_final' => new ResultatFinalResource($this->whenLoaded('resultatFinal')),
            'alerts' => AlertResource::collection($this->whenLoaded('alerts')),
        ];
    }
}
