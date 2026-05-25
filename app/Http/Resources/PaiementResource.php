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
            'candidature_id' => $this->candidature_id,
            'reference' => $this->reference,
            'montant' => (float) $this->montant,
            'preuve_paiement' => $this->preuve_paiement,

            // Données OCR
            'montant_ocr' => $this->montant_ocr ? (float) $this->montant_ocr : null,
            'date_ocr' => $this->date_ocr?->format('Y-m-d'),
            'banque_ocr' => $this->banque_ocr,
            'numero_compte_ocr' => $this->numero_compte_ocr,
            'reference_ocr' => $this->reference_ocr,
            'ocr_confidence' => $this->ocr_confidence ? (float) $this->ocr_confidence : null,
            'ocr_confidence_percent' => $this->ocrConfidencePercent(),
            'ocr_raw_data' => $this->when($request->input('include_ocr_raw'), $this->ocr_raw_data),
            'has_ocr_data' => $this->hasOcrData(),

            // Statut et validation
            'statut' => $this->statut,
            'statut_label' => $this->statut->label(),
            'is_en_attente' => $this->isEnAttente(),
            'is_ocr_verifie' => $this->isOcrVerifie(),
            'is_valide' => $this->isValide(),
            'is_rejete' => $this->isRejete(),
            'motif_rejet' => $this->motif_rejet,
            'validated_at' => $this->validated_at?->format('Y-m-d H:i:s'),
            'validated_by' => $this->validated_by,
            'validation_notes' => $this->validation_notes,

            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relations - always include essential data for validation pages
            'candidature' => $this->when(
                $this->relationLoaded('candidature') && $this->candidature,
                fn () => [
                    'id' => $this->candidature->id,
                    'code_cand_temp' => $this->candidature->code_cand_temp,
                    'code_cand_def' => $this->candidature->code_cand_def,
                    'statut_candidature' => $this->candidature->statut_candidature,
                    'paiement_valide' => $this->candidature->paiement_valide,
                    'documents_complets' => $this->candidature->documents_complets,
                    // Include essential candidat data
                    'candidat' => $this->when(
                        $this->candidature->relationLoaded('candidat') && $this->candidature->candidat,
                        fn () => [
                            'id' => $this->candidature->candidat->utilisateur_id,
                            'nom_cand' => $this->candidature->candidat->nom_cand,
                            'prenom_cand' => $this->candidature->candidat->prenom_cand,
                            'utilisateur' => $this->when(
                                $this->candidature->candidat->relationLoaded('utilisateur') && $this->candidature->candidat->utilisateur,
                                fn () => [
                                    'id' => $this->candidature->candidat->utilisateur->id,
                                    'email' => $this->candidature->candidat->utilisateur->email,
                                ]
                            ),
                        ]
                    ),
                    // Include essential concours data
                    'concours' => $this->when(
                        $this->candidature->relationLoaded('concours') && $this->candidature->concours,
                        fn () => [
                            'id' => $this->candidature->concours->id,
                            'libelle_concours' => $this->candidature->concours->libelle_concours,
                        ]
                    ),
                ]
            ),

            // Full resources - only when explicitly requested
            'candidat' => $this->when(
                $this->relationLoaded('candidat') && $request->input('include_candidat'),
                fn () => new CandidatResource($this->candidat)
            ),
            'concours' => $this->when(
                $this->relationLoaded('concours') && $request->input('include_concours'),
                fn () => new ConcoursResource($this->concours)
            ),
            'validateur' => $this->when(
                $this->relationLoaded('validatedBy') && $request->input('include_validateur'),
                fn () => new UtilisateurResource($this->validatedBy)
            ),
        ];
    }
}
