<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConcoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'spec_concours_id' => ['sometimes', 'uuid', 'exists:specs_concours,id'],
            'libelle_concours' => ['sometimes', 'string', 'max:255'],
            'date_limite_depot' => ['sometimes', 'date'],
            'date_examen' => ['sometimes', 'date'],
            'nbre_max_places' => ['sometimes', 'integer', 'min:1'],
            'frais_inscription' => ['sometimes', 'numeric', 'min:0'],
            'est_actif' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'spec_concours_id.uuid' => 'L\'identifiant de la spécification est invalide',
            'spec_concours_id.exists' => 'La spécification spécifiée n\'existe pas',
            'libelle_concours.max' => 'Le libellé ne peut pas dépasser 255 caractères',
            'nbre_max_places.min' => 'Le nombre de places doit être au moins 1',
            'frais_inscription.min' => 'Les frais doivent être positifs',
        ];
    }
}
