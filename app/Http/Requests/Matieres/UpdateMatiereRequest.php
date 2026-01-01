<?php

namespace App\Http\Requests\Matieres;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMatiereRequest extends FormRequest
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
        $matiereId = $this->route('matiere');
        
        return [
            'code_matiere' => 'required|string|max:10|unique:matieres,code_matiere,' . $matiereId,
            'libelle_matiere' => 'required|string|max:200',
            'coefficient' => 'nullable|integer|min:1|max:10',
            'est_actif' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code_matiere.required' => 'Le code de la matière est obligatoire',
            'code_matiere.unique' => 'Ce code matière existe déjà',
            'libelle_matiere.required' => 'Le libellé de la matière est obligatoire',
            'coefficient.integer' => 'Le coefficient doit être un nombre entier',
            'coefficient.min' => 'Le coefficient doit être supérieur ou égal à 1',
            'coefficient.max' => 'Le coefficient ne peut pas dépasser 10',
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
