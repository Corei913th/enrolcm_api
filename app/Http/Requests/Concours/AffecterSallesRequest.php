<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour l'affectation automatique aux salles.
 */
class AffecterSallesRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return true; // Géré par les middlewares d'authentification
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [
            'ordre_affectation' => [
                'sometimes',
                'string',
                'in:alphabetique,moyenne',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'ordre_affectation.in' => 'L\'ordre d\'affectation doit être "alphabetique" ou "moyenne".',
        ];
    }

    /**
     * Noms d'attributs personnalisés.
     */
    public function attributes(): array
    {
        return [
            'ordre_affectation' => 'ordre d\'affectation',
        ];
    }
}
