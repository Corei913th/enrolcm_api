<?php

namespace App\Http\Requests\Candidats;

use Illuminate\Foundation\Http\FormRequest;

class CompleteCandidatureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'centre_examen_id' => ['nullable', 'uuid', 'exists:centres,id'],
            'centre_depot_id' => ['nullable', 'uuid', 'exists:centres,id'],
            'date_depot_physique' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'centre_examen_id.uuid' => 'L\'identifiant du centre d\'examen est invalide',
            'centre_examen_id.exists' => 'Le centre d\'examen sélectionné n\'existe pas',
            'centre_depot_id.uuid' => 'L\'identifiant du centre de dépôt est invalide',
            'centre_depot_id.exists' => 'Le centre de dépôt sélectionné n\'existe pas',
            'date_depot_physique.date' => 'La date de dépôt physique doit être une date valide',
            'date_depot_physique.after_or_equal' => 'La date de dépôt physique ne peut pas être dans le passé',
        ];
    }
}
