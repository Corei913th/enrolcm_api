<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaiementResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'candidat_id' => $this->candidat_id,
            'concours_id' => $this->concours_id,
            'reference' => $this->reference, // PRU
            'montant' => (float) $this->montant,
            'preuve_paiement' => $this->preuve_paiement,
            
            // Données OCR
            'montant_ocr' => $this->montant_ocr ? (float) $this->montant_ocr : null,
            'date_ocr' => $this->date_ocr?->format('Y-m-d'),
            'banque_ocr' => $this->banque_ocr,
            'reference_ocr' => $this->reference_ocr,
            'ocr_confidence' => $this->ocr_confidence ? (float) $this->ocr_confidence : null,
            'ocr_raw_data' => $this->ocr_raw_data,
            
            // Statut et validation
            'statut' => $this->statut,
            'statut_label' => $this->statut->label(),
            'motif_rejet' => $this->motif_rejet,
            'validated_at' => $this->validated_at?->format('Y-m-d H:i:s'),
            'validated_by' => $this->validated_by,
            'rejected_at' => $this->rejected_at?->format('Y-m-d H:i:s'),
            'rejected_by' => $this->rejected_by,
            
            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relations
            'candidat' => new CandidatResource($this->whenLoaded('candidat')),
            'concours' => new ConcoursResource($this->whenLoaded('concours')),
            'validateur' => new UtilisateurResource($this->whenLoaded('validatedBy')),
        ];
    }
}
