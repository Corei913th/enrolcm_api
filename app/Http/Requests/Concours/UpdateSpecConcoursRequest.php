<?php

namespace App\Http\Requests\Concours;

use App\Enums\SerieBac;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpecConcoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('spec'); // Récupérer l'ID de la route

        return [
            'nom_spec' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('specs_concours', 'nom_spec')->ignore($id),
            ],
            'desc_infos_concours' => 'nullable|string',
            'documents_requis' => 'nullable|array',
            'documents_requis.*' => 'string',
            'montant_frais_depot' => 'nullable|numeric|min:0',
            'age_minimum' => 'nullable|integer|min:0',
            'age_maximum' => 'nullable|integer|min:0|gt:age_minimum', // gt:age_minimum fonctionnera si age_minimum est présent dans la request, sinon il faut gérer cas par cas
            'series_bac_acceptees' => 'nullable|array',
            'series_bac_acceptees.*' => ['string', Rule::in(SerieBac::values())],
            'nationalites_acceptees' => 'nullable|array',
            'nationalites_acceptees.*' => 'string',
            'est_actif' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_spec.unique' => 'Cette spécialité existe déjà.',
            'age_maximum.gt' => 'L\'âge maximum doit être supérieur à l\'âge minimum.',
            'series_bac_acceptees.*.in' => 'La série de baccalauréat sélectionnée est invalide.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validation conditionnelle avancée si nécessaire pour age_max > age_min quand un seul change
        });
    }
}
