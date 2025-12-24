<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'candidature_id' => $this->candidature_id,
            'fichier_url' => $this->fichier_url,
            'nom_original' => $this->nom_original,
            'type_document' => $this->type_document,
            'extension' => $this->getExtension(),
            'is_pdf' => $this->isPDF(),
            'is_image' => $this->isImage(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
