<?php

namespace App\Http\Requests\Planning;

use Illuminate\Foundation\Http\FormRequest;

class PlanifierEpreuveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'epreuve_id' => 'required|uuid|exists:epreuves,id_epreuve',
            'concours_id' => 'required|uuid|exists:concours,id',
            'session_id' => 'required|uuid|exists:sessions,id',
            'date_epreuve' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'instructions' => 'nullable|string',
            'est_actif' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'epreuve_id.required' => 'L\'ID de l\'épreuve est obligatoire',
            'epreuve_id.exists' => 'L\'épreuve spécifiée n\'existe pas',
            'concours_id.required' => 'L\'ID du concours est obligatoire',
            'concours_id.exists' => 'Le concours spécifié n\'existe pas',
            'session_id.required' => 'L\'ID de la session est obligatoire',
            'session_id.exists' => 'La session spécifiée n\'existe pas',
            'date_epreuve.required' => 'La date de l\'épreuve est obligatoire',
            'date_epreuve.date' => 'La date de l\'épreuve doit être une date valide',
            'heure_debut.required' => 'L\'heure de début est obligatoire',
            'heure_debut.date_format' => 'L\'heure de début doit être au format HH:MM',
            'heure_fin.required' => 'L\'heure de fin est obligatoire',
            'heure_fin.date_format' => 'L\'heure de fin doit être au format HH:MM',
            'heure_fin.after' => 'L\'heure de fin doit être après l\'heure de début',
        ];
    }
}
