<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartementResource extends JsonResource
{
    public function toArray($request)
    {
        // Extraire le code réel du département depuis le code composite
        $codeDepartementReel = $this->extraireCodeDepartement($this->code_departement);

        return [
            'id' => $this->id,
            'code_ecole' => $this->extraireCodeEcole($this->code_departement),
            'code_departement' => $codeDepartementReel,
            'code_composite' => $this->code_departement, // CODEECOLE + CODEDEPARTEMENT
            'libelle_departement' => $this->libelle_departement,
            'ecole_id' => $this->ecole_id,
            'desc_departement' => $this->desc_departement,
            'est_actif' => $this->est_actif,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            'ecole' => new EcoleResource($this->whenLoaded('ecole')),
            'filieres' => FiliereResource::collection($this->whenLoaded('filieres')),
        ];
    }

    /**
     * Extraire le code département depuis le code composite.
     * Format: CODEECOLE-CODEDEPARTEMENT
     */
    private function extraireCodeDepartement(string $codeComposite): string
    {
        $parts = explode('-', $codeComposite, 2);
        return $parts[1] ?? $codeComposite;
    }

    /**
     * Extraire le code école depuis le code composite.
     */
    private function extraireCodeEcole(string $codeComposite): string
    {
        $parts = explode('-', $codeComposite, 2);
        return $parts[0] ?? $codeComposite;
    }
}
