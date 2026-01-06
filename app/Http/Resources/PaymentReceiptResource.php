<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentReceiptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero_recu' => $this->numero_recu,
            'banque' => $this->banque,
            'montant' => $this->montant,
            'date_paiement' => $this->date_paiement?->format('Y-m-d'),
            'statut_verification' => [
                'value' => $this->statut_verification->value,
                'label' => $this->statut_verification->label(),
            ],
            'motif_rejet' => $this->motif_rejet,
            'verified_at' => $this->verified_at?->format('Y-m-d H:i:s'),
            'verified_by' => $this->whenLoaded('verifiedBy', function () {
                return [
                    'id' => $this->verifiedBy->id,
                    'user_name' => $this->verifiedBy->user_name,
                ];
            }),
            'candidat' => $this->whenLoaded('candidat', function () {
                return [
                    'id' => $this->candidat->utilisateur_id,
                    'nom' => $this->candidat->nom_cand,
                    'prenom' => $this->candidat->prenom_cand,
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
