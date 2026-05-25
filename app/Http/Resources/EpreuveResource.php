<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EpreuveResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id_epreuve' => $this->id_epreuve,
            'intitule' => $this->intitule,
            'session' => $this->session,
            'url_epreuve' => $this->url_epreuve,
            'type_epreuve' => $this->type_epreuve?->value,
            'type_label' => $this->type_epreuve?->label(),
            'duree_en_minute' => $this->duree_en_minute,
            'duree_formatee' => $this->getDureeFormatee(),
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];

        // Add planning data if available
        if ($this->relationLoaded('plannings') && $this->plannings->isNotEmpty()) {
            $planning = $this->plannings->first();
            $data['date_epreuve'] = $planning->date_epreuve?->format('Y-m-d');
            $data['heure_debut'] = $planning->heure_debut;
            $data['heure_fin'] = $planning->heure_fin;
            $data['planning_id'] = $planning->id;
        }

        return $data;
    }
}
