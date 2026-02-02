<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\SerieBac;

class CreateSpecConcoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_spec' => 'required|string|max:255|unique:specs_concours,nom_spec',
            'desc_infos_concours' => 'nullable|string',
            'documents_requis' => 'nullable|array',
            'documents_requis.*' => 'string',
            'montant_frais_depot' => 'nullable|numeric|min:0',
            'age_minimum' => 'nullable|integer|min:0',
            'age_maximum' => 'nullable|integer|min:0|gt:age_minimum',
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
            'nom_spec.required' => 'Le nom de la spécialité est obligatoire.',
            'nom_spec.unique' => 'Cette spécialité existe déjà.',
            'age_maximum.gt' => 'L\'âge maximum doit être supérieur à l\'âge minimum.',
        ];
    }
}
