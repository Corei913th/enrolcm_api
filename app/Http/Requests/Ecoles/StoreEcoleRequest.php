<?php

namespace App\Http\Requests\Ecoles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
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
            'region' => ['required', new Enum(RegionCameroun::class)],
            'localisation' => 'nullable|string|max:200',
            
            // Fichiers et médias
            'logo_url' => 'nullable|string|max:500',
            'embleme_ecole' => 'nullable|string|max:500',
            'photo_facade' => 'nullable|string|max:500',
            'document_agrement' => 'nullable|string|max:500',
            
            // Informations de contact
            'bp_ecole' => 'nullable|string|max:50',
            'email_ecole' => 'nullable|email|max:100',
            'siteweb_ecole' => 'nullable|url|max:200',
            'telephone_ecole' => 'nullable|string|max:20',
            'fax_ecole' => 'nullable|string|max:20',
            
            // Informations administratives
            'devise' => 'nullable|string|max:200',
            'directeur_nom' => 'nullable|string|max:100',
            'directeur_email' => 'nullable|email|max:100',
            'directeur_telephone' => 'nullable|string|max:20',
            
            // Informations légales
            'numero_agrement' => 'nullable|string|max:50',
            'date_creation' => 'nullable|date',
            'type_etablissement' => 'nullable|in:public,prive',
            
            // Statut et métadonnées
            'est_actif' => 'nullable|boolean',
            'description' => 'nullable|string',
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
            'directeur_email.email' => 'L\'email du directeur doit être valide',
            'siteweb_ecole.url' => 'Le site web doit être une URL valide',
            'date_creation.date' => 'La date de création doit être une date valide',
            'type_etablissement.in' => 'Le type d\'établissement doit être public ou privé',
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
