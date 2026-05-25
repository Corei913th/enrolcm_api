<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour la saisie d'une note.
 */
class SaisirNoteRequest extends FormRequest
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
            'candidature_id' => [
                'required',
                'uuid',
                'exists:candidatures,id',
            ],
            'epreuve_id' => [
                'required',
                'uuid',
                'exists:epreuves,id_epreuve',
            ],
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
            'candidature_id.required' => 'L\'ID de la candidature est obligatoire.',
            'candidature_id.exists' => 'La candidature spécifiée n\'existe pas.',
            'epreuve_id.required' => 'L\'ID de l\'épreuve est obligatoire.',
            'epreuve_id.exists' => 'L\'épreuve spécifiée n\'existe pas.',
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
            'candidature_id' => 'candidature',
            'epreuve_id' => 'épreuve',
            'valeur' => 'note',
            'est_eliminatoire' => 'éliminatoire',
        ];
    }
}
