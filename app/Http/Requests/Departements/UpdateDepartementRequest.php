<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartementRequest extends FormRequest
{
    public function authorize(): bool
    {
        
        return true;
    }

    public function rules(): array
    {
        $departementId = $this->route('departement'); // UUID de la route
        $ecoleId = $this->input('ecole_id');

        return [
            'code_departement' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('departements', 'code_departement')
                    ->where(fn ($q) => $q->where('ecole_id', $ecoleId))
                    ->ignore($departementId),
            ],
            'libelle_departement' => 'required|string|min:3|max:200',
            'ecole_id' => 'required|uuid|exists:ecoles,id',
            'desc_departement' => 'nullable|string|max:1000',
            'est_actif' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code_departement.required' => 'Le code du département est obligatoire.',
            'code_departement.string' => 'Le code du département doit être une chaîne de caractères.',
            'code_departement.max' => 'Le code du département ne peut pas dépasser 10 caractères.',
            'code_departement.regex' => 'Le code doit contenir uniquement des lettres majuscules, chiffres, tirets et underscores.',
            'code_departement.unique' => 'Ce code de département existe déjà dans cette école.',

            'libelle_departement.required' => 'Le libellé du département est obligatoire.',
            'libelle_departement.string' => 'Le libellé doit être une chaîne de caractères.',
            'libelle_departement.min' => 'Le libellé doit contenir au moins 3 caractères.',
            'libelle_departement.max' => 'Le libellé ne peut pas dépasser 200 caractères.',

            'ecole_id.required' => 'L\'école est obligatoire.',
            'ecole_id.exists' => 'L\'école spécifiée n\'existe pas.',

            'desc_departement.string' => 'La description doit être une chaîne de caractères.',
            'desc_departement.max' => 'La description ne peut pas dépasser 1000 caractères.',

            'est_actif.boolean' => 'Le statut doit être vrai ou faux.',
        ];
    }
}
