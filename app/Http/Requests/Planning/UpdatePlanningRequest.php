<?php

namespace App\Http\Requests\Planning;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanningRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'date_epreuve' => 'sometimes|date',
      'heure_debut' => 'sometimes|date_format:H:i',
      'heure_fin' => 'sometimes|date_format:H:i|after:heure_debut',
      'instructions' => 'nullable|string',
      'est_actif' => 'sometimes|boolean',
    ];
  }

  public function messages(): array
  {
    return [
      'date_epreuve.date' => 'La date de l\'épreuve doit être une date valide',
      'heure_debut.date_format' => 'L\'heure de début doit être au format HH:MM',
      'heure_fin.date_format' => 'L\'heure de fin doit être au format HH:MM',
      'heure_fin.after' => 'L\'heure de fin doit être après l\'heure de début',
    ];
  }
}
