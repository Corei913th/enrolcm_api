<?php

namespace App\Http\Requests\Epreuves;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TypeEpreuve;

class UpdateEpreuveRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'intitule' => 'sometimes|string|max:200',
      'session' => 'sometimes|string|max:255',
      'url_epreuve' => 'nullable|url',
      'fichier_epreuve' => 'nullable|file|mimes:pdf,doc,docx,odt,rtf,txt|max:2048',
      'type_epreuve' => 'sometimes|in:' . implode(',', TypeEpreuve::values()),
      'duree_en_minute' => 'sometimes|integer|min:1',
      'est_actif' => 'sometimes|boolean',
    ];
  }

  public function messages(): array
  {
    return [
      'intitule.max' => 'L\'intitulé ne peut pas dépasser 200 caractères',
      'url_epreuve.url' => 'L\'URL de l\'épreuve doit être valide',
      'type_epreuve.in' => 'Le type d\'épreuve n\'est pas valide',
      'duree_en_minute.integer' => 'La durée doit être un nombre entier',
      'duree_en_minute.min' => 'La durée doit être d\'au moins 1 minute',
    ];
  }
}
