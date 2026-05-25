<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour marquer un candidat comme présent.
 */
class MarquerPresentRequest extends FormRequest
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
            'heure_arrivee' => [
                'sometimes',
                'date_format:H:i',
            ],
            'observations' => [
                'sometimes',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'heure_arrivee.date_format' => 'L\'heure d\'arrivée doit être au format HH:MM.',
            'observations.string' => 'Les observations doivent être une chaîne de caractères.',
            'observations.max' => 'Les observations ne peuvent pas dépasser 500 caractères.',
        ];
    }

    /**
     * Noms d'attributs personnalisés.
     */
    public function attributes(): array
    {
        return [
            'heure_arrivee' => 'heure d\'arrivée',
            'observations' => 'observations',
        ];
    }
}
