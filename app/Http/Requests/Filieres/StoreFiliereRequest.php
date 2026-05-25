<?php

namespace App\Http\Requests\Filieres;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreFiliereRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'code_filiere' => [
                'required',
                'string',
                'max:10',
                Rule::unique('filieres', 'code_filiere')
                    ->where(fn ($q) => $q->where('departement_id', request('departement_id'))),
            ],
            'libelle_filiere' => 'required|string|max:200',
            'departement_id' => 'required|uuid|exists:departements,id',
            'desc_filiere' => 'nullable|string',
            'est_actif' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code_filiere.required' => 'Le code de la filière est obligatoire',
            'code_filiere.unique' => 'Cette filière existe déjà  pour ce département',
            'libelle_filiere.required' => 'Le libellé de la filière est obligatoire',
            'departement_id.exists' => 'Le département sélectionné est invalide',
            'departement_id.uuid' => 'L\'identifiant du département est invalide',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            api_validation_error($validator->errors(), 'Erreur de validation')
        );
    }
}
