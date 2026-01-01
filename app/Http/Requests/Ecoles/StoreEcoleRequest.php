<?php

namespace App\Http\Requests\Ecoles;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\RegionCameroun;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEcoleRequest extends FormRequest
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
        return [
            'code_ecole' => 'required|string|max:20|unique:ecoles,code_ecole',
            'libelle_ecole' => 'required|string|max:200',
            'region' => ['required', 'in:' . implode(',', RegionCameroun::values())],
            'localisation' => 'nullable|string|max:200',
            'email_ecole' => 'nullable|email|max:100',
            'telephone_ecole' => 'nullable|string|max:20',
            'siteweb_ecole' => 'nullable|url|max:200',
            'devise' => 'nullable|string|max:100',
            'bp_ecole' => 'nullable|string|max:50',
            'logo_url' => 'nullable|string|max:500',
            'embleme_ecole' => 'nullable|string|max:500',
            'est_actif' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code_ecole.required' => 'Le code de l\'école est obligatoire',
            'code_ecole.unique' => 'Ce code école existe déjà',
            'libelle_ecole.required' => 'Le libellé de l\'école est obligatoire',
            'region.required' => 'La région est obligatoire',
            'email_ecole.email' => 'L\'email doit être valide',
            'siteweb_ecole.url' => 'Le site web doit être une URL valide',
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