<?php

namespace App\Http\Requests\Filieres;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreFiliereRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code_filiere' => 'required|string|max:10|unique:filieres,code_filiere',
            'libelle_filiere' => 'required|string|max:200',
            'departement_id' => 'nullable|uuid|exists:departements,id',
            'desc_filiere' => 'nullable|string',
            'est_actif' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code_filiere.required' => 'Le code de la filière est obligatoire',
            'code_filiere.unique' => 'Cette filière existe déjà',
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
