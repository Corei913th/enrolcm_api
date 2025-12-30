<?php

namespace App\Http\Requests\Departements;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDepartementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departementId = $this->route('departement');
        
        return [
            'code_departement' => 'required|string|max:10|unique:departements,code_departement,' . $departementId,
            'libelle_departement' => 'required|string|max:200',
            'ecole_id' => 'nullable|uuid|exists:ecoles,id',
            'desc_departement' => 'nullable|string',
            'est_actif' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code_departement.required' => 'Le code du département est obligatoire',
            'code_departement.unique' => 'Ce code département existe déjà',
            'libelle_departement.required' => 'Le libellé du département est obligatoire',
            'ecole_id.exists' => 'L\'école sélectionnée n\'existe pas',
            'ecole_id.uuid' => 'L\'identifiant de l\'école est invalide',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            api_validation_error($validator->errors(), 'Erreur de validation')
        );
    }
}
