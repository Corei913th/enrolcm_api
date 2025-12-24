<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\TypeEpreuve;

class EpreuveResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_epreuve' => $this->id_epreuve,
            'intitule' => $this->intitule,
            'session' => $this->session,
            'url_epreuve' => $this->url_epreuve,
            'type_epreuve' => $this->type_epreuve,
            'type_label' => TypeEpreuve::label($this->type_epreuve),
            'duree_en_minute' => $this->duree_en_minute,
            'duree_formatee' => $this->getDureeFormatee(),
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
