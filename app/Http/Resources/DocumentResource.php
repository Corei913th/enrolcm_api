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
            'document_requis_id' => $this->document_requis_id,
            'fichier_url' => $this->fichier_url,
            'nom_original' => $this->nom_original,
            'type_document' => $this->type_document,
            'statut_verification' => $this->statut_verification,
            'statut_verification_label' => $this->statut_verification?->label(),
            'commentaire_verification' => $this->commentaire_verification,
            'valide_par' => $this->valide_par,
            'date_verification' => $this->date_verification?->format('Y-m-d H:i:s'),
            'extension' => $this->getExtension(),
            'taille' => $this->getTaille(),
            'taille_formatee' => $this->getTailleFormatee(),
            'is_pdf' => $this->isPDF(),
            'is_image' => $this->isImage(),
            'is_valide' => $this->isValide(),
            'is_rejete' => $this->isRejete(),
            'est_en_attente' => $this->estEnAttente(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),

            // Relations
            'document_requis' => $this->whenLoaded('documentRequis'),
            'validateur' => new UtilisateurResource($this->whenLoaded('validePar')),
        ];
    }
}
