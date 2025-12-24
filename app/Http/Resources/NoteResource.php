<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\StatutNote;

class NoteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'candidature_id' => $this->candidature_id,
            'epreuve_id' => $this->epreuve_id,
            'valeur' => $this->valeur,
            'date_saisie' => $this->date_saisie?->format('Y-m-d H:i:s'),
            'est_definitive' => $this->est_definitive,
            'est_eliminatoire' => $this->est_eliminatoire,
            'statut' => $this->statut,
            'statut_label' => StatutNote::label($this->statut),
            'is_valide' => $this->isValide(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),          
            'epreuve' => new EpreuveResource($this->whenLoaded('epreuve')),
        ];
    }
}
