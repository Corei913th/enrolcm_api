<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

class CreateConcoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'spec_concours_id' => ['sometimes', 'uuid', 'exists:specs_concours,id'],
            'libelle_concours' => ['required', 'string', 'max:255'],
            'date_debut' => ['nullable', 'date', 'after:today'],
            'date_limite_depot' => ['nullable', 'date', 'after:date_debut'],
            'nombre_places' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
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
            'libelle_concours.max' => 'Le libellé ne peut pas dépasser 255 caractères',
            'date_debut.after' => 'La date d\'examen doit être dans le futur',
            'date_limite_depot.after' => 'La date limite doit être après la date d\'examen',
            'nombre_places.min' => 'Le nombre de places doit être au moins 1',
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

            // Si session_id est fourni, valider cohérence dates
            if (!empty($data['session_id'])) {
                if (!empty($data['date_limite_depot']) && !empty($data['date_debut'])) {
                    if ($data['date_limite_depot'] <= $data['date_debut']) {
                        $validator->errors()->add('date_limite_depot', 'La date limite doit être après la date d\'examen');
                    }
                }
            }
        });
    }
}
