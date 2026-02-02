<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConcoursPaiementResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'concours_id' => $this->concours_id,
            'banque_nom' => $this->banque_nom,
            'numero_compte' => $this->numero_compte,
            'nom_beneficiaire' => $this->nom_beneficiaire,
            'montant' => $this->montant,
            'date_limite' => $this->date_limite?->format('Y-m-d'),
            'instructions' => $this->instructions,
            'devise' => $this->devise,
            'code_banque' => $this->code_banque,
            'agence_banque' => $this->agence_banque,
            'iban' => $this->iban,
            'type_paiement' => $this->type_paiement,
            'banques_acceptees' => $this->banques_acceptees,
            'frais_paiement' => $this->frais_paiement,
            'reference_format' => $this->reference_format,
            'minimum_confiance_ocr' => $this->minimum_confiance_ocr,
            'validation_auto' => $this->validation_auto,
            'commentaires' => $this->commentaires,
            'date_derniere_modification' => $this->date_derniere_modification?->format('Y-m-d H:i:s'),
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
