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
            'spec_concours_id' => ['nullable', 'uuid', 'exists:specs_concours,id'], // OPTIONNEL
            'libelle_concours' => ['required', 'string', 'max:255'],
            'date_debut' => ['nullable', 'date', 'after:today'], // OPTIONNEL si pas de session
            'date_limite_depot' => ['nullable', 'date', 'after:date_debut'], // OPTIONNEL si pas de session
            'nombre_places' => ['nullable', 'integer', 'min:1'], // OPTIONNEL si pas de session
            'description' => ['nullable', 'string'],
            'session_id' => ['nullable', 'uuid', 'exists:sessions,id'], // OPTIONNEL
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

            // Si session_id est fourni, tous les champs liés doivent l'être
            if (!empty($data['session_id'])) {
                $requiredWithSession = ['date_debut', 'date_limite_depot', 'nombre_places'];

                foreach ($requiredWithSession as $field) {
                    if (empty($data[$field])) {
                        $validator->errors()->add($field, "Le champ {$field} est obligatoire quand une session est spécifiée");
                    }
                }
            }

            // Si aucun champ de session n'est fourni, c'est un concours "template"
            // Tous les champs peuvent être null
        });
    }
}
