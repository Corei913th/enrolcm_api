<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour la modification d'une note.
 */
class ModifierNoteRequest extends FormRequest
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
            'valeur' => [
                'required',
                'numeric',
                'min:0',
                'max:20',
            ],
            'est_eliminatoire' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'valeur.required' => 'La valeur de la note est obligatoire.',
            'valeur.numeric' => 'La note doit être un nombre.',
            'valeur.min' => 'La note ne peut pas être inférieure à 0.',
            'valeur.max' => 'La note ne peut pas être supérieure à 20.',
            'est_eliminatoire.boolean' => 'Le champ éliminatoire doit être un booléen.',
        ];
    }

    /**
     * Noms d'attributs personnalisés.
     */
    public function attributes(): array
    {
        return [
            'valeur' => 'note',
            'est_eliminatoire' => 'éliminatoire',
        ];
    }
}
