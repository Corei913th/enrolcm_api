<?php

namespace App\Http\Requests\Ecoles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\RegionCameroun;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEcoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $ecoleId = $this->route('ecole');

        return [
            // Informations de base
            'code_ecole' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('ecoles', 'code_ecole')->ignore($ecoleId)
            ],
            'libelle_ecole' => 'sometimes|required|string|max:200',
            'libelle_ecole_en' => 'nullable|string|max:200',


            'region' => ['sometimes', 'required', 'in:' . implode(',', RegionCameroun::values())],
            'localisation' => 'nullable|string|max:200',
            'adresse_complete' => 'nullable|string|max:500',
            'ville' => 'nullable|string|max:100',

            // Contact
            'telephone_ecole' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'telephone_2' => 'nullable|string|max:20',
            'email_ecole' => 'nullable|email|max:100',
            'siteweb_ecole' => 'nullable|url|max:200',
            'bp_ecole' => 'nullable|string|max:50',

            // Médias - URLs externes OU fichiers uploadés (mutuellement exclusifs)
            'logo_url' => 'nullable|string|max:500|prohibited_if:logo,*,embleme,*,header_frame,*',
            'embleme_ecole' => 'nullable|string|max:500|prohibited_if:logo,*,embleme,*,header_frame,*',
            'logo' => 'nullable|file|image|max:5120|prohibited_if:logo_url,*,embleme_ecole,*', // 5MB max
            'embleme' => 'nullable|file|image|max:5120|prohibited_if:logo_url,*,embleme_ecole,*', // 5MB max
            'header_frame' => 'nullable|file|image|max:5120|prohibited_if:logo_url,*,embleme_ecole,*', // 5MB max

            // Identité visuelle
            'devise' => 'nullable|string|max:100',
            'slogan' => 'nullable|string|max:200',


            // Institution tutelle
            'nom_institution_tutelle' => 'nullable|string|max:200',
            'nom_institution_tutelle_en' => 'nullable|string|max:200',
            'numero_agrement' => 'nullable|string|max:100',
            'date_creation' => 'nullable|date|before:today',
            'logo_institution_tutelle_url' => 'nullable|string|max:500',


            // Statut
            'est_actif' => 'nullable|boolean',

            // Mentions légales
            'mentions_legales' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Informations de base
            'code_ecole.required' => 'Le code de l\'école est obligatoire',
            'code_ecole.unique' => 'Ce code école existe déjà',
            'libelle_ecole.required' => 'Le libellé de l\'école est obligatoire',

            // Localisation
            'region.required' => 'La région est obligatoire',
            'ville.max' => 'Le nom de la ville ne peut pas dépasser 100 caractères',

            // Contact
            'telephone_ecole.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères',
            'fax.max' => 'Le numéro de fax ne peut pas dépasser 20 caractères',
            'telephone_2.max' => 'Le deuxième numéro de téléphone ne peut pas dépasser 20 caractères',
            'email_ecole.email' => 'L\'email doit être valide',
            'siteweb_ecole.url' => 'Le site web doit être une URL valide',

            // Fichiers
            'logo.file' => 'Le logo doit être un fichier',
            'logo.image' => 'Le logo doit être une image',
            'logo.max' => 'Le logo ne peut pas dépasser 5MB',
            'embleme.file' => 'L\'emblème doit être un fichier',
            'embleme.image' => 'L\'emblème doit être une image',
            'embleme.max' => 'L\'emblème ne peut pas dépasser 5MB',
            'header_frame.file' => 'L\'entête doit être un fichier',
            'header_frame.image' => 'L\'entête doit être une image',
            'header_frame.max' => 'L\'entête ne peut pas dépasser 5MB',

            // Règles d'exclusion mutuelle
            'logo_url.prohibited_if' => 'Vous ne pouvez pas fournir à la fois une URL et un fichier pour le logo',
            'embleme_ecole.prohibited_if' => 'Vous ne pouvez pas fournir à la fois une URL et un fichier pour l\'emblème',
            'logo.prohibited_if' => 'Vous ne pouvez pas fournir à la fois un fichier et une URL pour le logo',
            'embleme.prohibited_if' => 'Vous ne pouvez pas fournir à la fois un fichier et une URL pour l\'emblème',
            'header_frame.prohibited_if' => 'Vous ne pouvez pas fournir à la fois un fichier et une URL pour l\'entête',

            // Identité visuelle
            'devise.max' => 'La devise ne peut pas dépasser 100 caractères',
            'slogan.max' => 'Le slogan ne peut pas dépasser 200 caractères',

            // Direction
            'nom_directeur.max' => 'Le nom du directeur ne peut pas dépasser 100 caractères',
            'titre_directeur.max' => 'Le titre du directeur ne peut pas dépasser 100 caractères',

            // Institution tutelle
            'nom_institution_tutelle.max' => 'Le nom de l\'institution tutelle ne peut pas dépasser 200 caractères',
            'numero_agrement.max' => 'Le numéro d\'agrément ne peut pas dépasser 100 caractères',
            'date_creation.before' => 'La date de création doit être antérieure à aujourd\'hui',

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
