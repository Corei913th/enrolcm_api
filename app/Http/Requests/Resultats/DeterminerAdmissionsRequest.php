<?php

namespace App\Http\Requests\Resultats;

use Illuminate\Foundation\Http\FormRequest;

class DeterminerAdmissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Géré par le middleware
    }

    public function rules(): array
    {
        return [
            'filiere_id' => 'required|uuid|exists:filieres,id',
            'force' => 'sometimes|boolean',
            // Exemple: {"CENTRE": 50, "LITTORAL": 40}
            // Chaque valeur représente le nombre maximal d'admis dans la filière pour la région.
            'max_par_region' => 'sometimes|array',
            'max_par_region.*' => 'integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'filiere_id.required' => 'L\'identifiant de la filière est obligatoire',
            'filiere_id.uuid' => 'L\'identifiant de la filière doit être un UUID valide',
            'filiere_id.exists' => 'La filière spécifiée n\'existe pas dans la base de données',
            'force.boolean' => 'Le paramètre force doit être un booléen (true ou false)',
            'max_par_region.array' => 'max_par_region doit être un objet (clé=region, valeur=max)',
            'max_par_region.*.integer' => 'Chaque quota de max_par_region doit être un nombre entier',
            'max_par_region.*.min' => 'Chaque quota de max_par_region doit être >= 0',
        ];
    }

    public function attributes(): array
    {
        return [
            'filiere_id' => 'identifiant de la filière',
            'force' => 'forcer le recalcul',
            'max_par_region' => 'quotas région (max)',
        ];
    }
}
