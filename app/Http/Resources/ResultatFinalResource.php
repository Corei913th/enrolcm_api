<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\DecisionAdmission;
use App\Enums\Mention;

class ResultatFinalResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'candidature_id' => $this->candidature_id,
            'moyenne_generale' => $this->moyenne_generale,
            'total_point' => $this->total_point,
            'rang' => $this->rang,
            'decision' => $this->decision,
            'decision_label' => $this->decision ? DecisionAdmission::label($this->decision) : null,
            'mention' => $this->mention,
            'mention_label' => $this->mention ? Mention::label($this->mention) : null,
            'est_admis' => $this->est_admis,
            'date_publication' => $this->date_publication?->format('Y-m-d'),
            'is_admis_definitif' => $this->isAdmisDefinitif(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
