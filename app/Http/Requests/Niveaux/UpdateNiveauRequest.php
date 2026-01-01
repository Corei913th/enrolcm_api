<?php

namespace App\Http\Requests\Niveaux;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateNiveauRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $niveauId = $this->route('niveau');
        
        return [
            'code_niveau' => 'required|string|max:10|unique:niveaux,code_niveau,' . $niveauId,
            'libelle_niveau' => 'required|string|max:100',
            'filiere_id' => 'nullable|uuid|exists:filieres,id',
            'ordre' => 'nullable|integer|min:1',
            'desc_niveau' => 'nullable|string',
            'est_actif' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code_niveau.required' => 'Le code du niveau est obligatoire',
            'code_niveau.unique' => 'Ce code niveau existe déjà',
            'libelle_niveau.required' => 'Le libellé du niveau est obligatoire',
            'filiere_id.exists' => 'La filière sélectionnée n\'existe pas',
            'ordre.integer' => 'L\'ordre doit être un nombre entier',
            'ordre.min' => 'L\'ordre doit être supérieur ou égal à 1',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            api_validation_error($validator->errors(), 'Erreur de validation')
        );
    }
}
