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
            'spec_concours_id' => ['required', 'uuid', 'exists:specs_concours,id'],
            'libelle_concours' => ['required', 'string', 'max:255'],
            'date_debut' => ['required', 'date', 'after:today'], // Sera mappé vers date_examen
            'date_limite_depot' => ['required', 'date', 'after:date_debut'],
            'nombre_places' => ['nullable', 'integer', 'min:1'], // Sera mappé vers nbre_max_places
            'description' => ['nullable', 'string'],
            'session_id' => ['required', 'uuid', 'exists:sessions,id'],
            'est_actif' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'spec_concours_id.required' => 'L\'identifiant de la spécification est obligatoire',
            'spec_concours_id.uuid' => 'L\'identifiant de la spécification est invalide',
            'spec_concours_id.exists' => 'La spécification spécifiée n\'existe pas',
            'libelle_concours.required' => 'Le libellé du concours est obligatoire',
            'libelle_concours.max' => 'Le libellé ne peut pas dépasser 255 caractères',
            'date_debut.required' => 'La date d\'examen est obligatoire',
            'date_debut.after' => 'La date d\'examen doit être dans le futur',
            'date_limite_depot.required' => 'La date limite de dépôt est obligatoire',
            'date_limite_depot.after' => 'La date limite doit être après la date d\'examen',
            'nombre_places.min' => 'Le nombre de places doit être au moins 1',
            'session_id.required' => 'La session est obligatoire',
            'session_id.uuid' => 'L\'identifiant de session est invalide',
            'session_id.exists' => 'La session spécifiée n\'existe pas',
        ];
    }
}
