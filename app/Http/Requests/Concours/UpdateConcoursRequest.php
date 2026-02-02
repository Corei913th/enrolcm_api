<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConcoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prépare les données pour validation (sanitisation)
     */
    protected function prepareForValidation()
    {
        // Nettoyer la description si elle est vide ou ne contient que des espaces
        if ($this->has('description')) {
            $description = trim($this->input('description'));
            $this->merge([
                'description' => empty($description) ? null : $description
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'spec_concours_id' => ['sometimes', 'uuid', 'exists:specs_concours,id'],
            'libelle_concours' => [
                'sometimes',
                'string',
                'max:255',
                'min:3',
                'regex:/^[\p{L}\p{N}\s\-\'&,()]+$/u'
            ],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            // date_examen removed - use planning_epreuves as single source of truth
            'date_limite_depot' => ['sometimes', 'date', 'after:today', 'before:+2 years'],
            'nombre_places' => ['sometimes', 'integer', 'min:1', 'max:100000'],
            'session_id' => ['sometimes', 'uuid', 'exists:sessions,id'],
            'est_actif' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'spec_concours_id.uuid' => 'L\'identifiant de la spécification est invalide',
            'spec_concours_id.exists' => 'La spécification spécifiée n\'existe pas',
            'libelle_concours.min' => 'Le libellé doit contenir au moins 3 caractères',
            'libelle_concours.max' => 'Le libellé ne peut pas dépasser 255 caractères',
            'libelle_concours.regex' => 'Le libellé contient des caractères non autorisés',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères',
            'date_limite_depot.after' => 'La date limite de dépôt doit être dans le futur',
            'date_limite_depot.before' => 'La date limite de dépôt ne peut pas être dans plus de 2 ans',
            'nombre_places.min' => 'Le nombre de places doit être au moins 1',
            'nombre_places.max' => 'Le nombre de places ne peut pas dépasser 100 000',
            'session_id.uuid' => 'L\'identifiant de session est invalide',
            'session_id.exists' => 'La session spécifiée n\'existe pas',
        ];
    }

    /**
     * Validation métier personnalisée pour les updates
     * Note: date_examen validation removed - use planning_epreuves as single source of truth
     */
    public function withValidator($validator)
    {
        // No additional validation needed - date_limite_depot is validated independently
    }
}
