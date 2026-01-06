<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

class CreateConcoursRequest extends FormRequest
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
        $this->merge([
            'libelle_concours' => $this->sanitizeString($this->input('libelle_concours')),
            'description' => $this->sanitizeString($this->input('description')),
        ]);
    }

    /**
     * Nettoie une chaîne de caractères
     */
    private function sanitizeString(?string $value): ?string
    {
        if (!$value) {
            return $value;
        }

        // Supprime les espaces multiples et les espaces en début/fin
        $value = trim(preg_replace('/\s+/', ' ', $value));

        // Limite la longueur à 1000 caractères pour éviter les attaques
        if (strlen($value) > 1000) {
            $value = substr($value, 0, 1000);
        }

        return $value;
    }

    public function rules(): array
    {
        return [
            'spec_concours_id' => ['sometimes', 'uuid', 'exists:specs_concours,id'],
            'libelle_concours' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[\p{L}\p{N}\s\-\'&,()]+$/u' // Lettres, chiffres, espaces, caractères spéciaux sûrs
            ],
            'date_debut' => ['nullable', 'date', 'after:today', 'before:+2 years'],
            'date_limite_depot' => ['nullable', 'date', 'after:today', 'before:+2 years', 'before:date_debut'],
            'nombre_places' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'session_id' => ['sometimes', 'uuid', 'exists:sessions,id'],
            'est_actif' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'spec_concours_id.uuid' => 'L\'identifiant de la spécification est invalide',
            'spec_concours_id.exists' => 'La spécification spécifiée n\'existe pas',
            'libelle_concours.required' => 'Le libellé du concours est obligatoire',
            'libelle_concours.min' => 'Le libellé doit contenir au moins 3 caractères',
            'libelle_concours.max' => 'Le libellé ne peut pas dépasser 255 caractères',
            'libelle_concours.regex' => 'Le libellé contient des caractères non autorisés',
            'date_debut.after' => 'La date d\'examen doit être dans le futur',
            'date_debut.before' => 'La date d\'examen ne peut pas être dans plus de 2 ans',
            'date_limite_depot.after' => 'La date limite de dépôt doit être dans le futur',
            'date_limite_depot.before' => 'La date limite de dépôt doit être antérieure à la date d\'examen et ne peut pas être dans plus de 2 ans',
            'nombre_places.min' => 'Le nombre de places doit être au moins 1',
            'nombre_places.max' => 'Le nombre de places ne peut pas dépasser 100 000',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères',
            'session_id.uuid' => 'L\'identifiant de session est invalide',
            'session_id.exists' => 'La session spécifiée n\'existe pas',
        ];
    }

    /**
     * Validation métier personnalisée
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            // WORKFLOW SIMPLIFIÉ :
            // - spec_concours_id seul : concours template
            // - session_id seul : concours avec session (dates/places optionnelles)
            // - spec + session : concours complet

            // Valider cohérence dates : date_limite_depot doit être AVANT date_debut (date d'examen)
            if (!empty($data['date_limite_depot']) && !empty($data['date_debut'])) {
                if ($data['date_limite_depot'] >= $data['date_debut']) {
                    $validator->errors()->add('date_limite_depot', 'La date limite de dépôt doit être antérieure à la date d\'examen');
                }
            }
        });
    }
}
