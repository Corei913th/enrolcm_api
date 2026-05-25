<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour la réaffectation d'un candidat.
 */
class ReaffecterCandidatRequest extends FormRequest
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
            'nouvelle_salle_id' => [
                'required',
                'uuid',
                'exists:salles_examen,id',
            ],
            'nouveau_numero_place' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'nouvelle_salle_id.required' => 'L\'ID de la nouvelle salle est obligatoire.',
            'nouvelle_salle_id.exists' => 'La salle spécifiée n\'existe pas.',
            'nouveau_numero_place.required' => 'Le numéro de place est obligatoire.',
            'nouveau_numero_place.integer' => 'Le numéro de place doit être un entier.',
            'nouveau_numero_place.min' => 'Le numéro de place doit être au minimum 1.',
        ];
    }

    /**
     * Noms d'attributs personnalisés.
     */
    public function attributes(): array
    {
        return [
            'nouvelle_salle_id' => 'nouvelle salle',
            'nouveau_numero_place' => 'numéro de place',
        ];
    }
}
