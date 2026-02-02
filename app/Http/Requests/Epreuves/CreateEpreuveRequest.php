<?php

namespace App\Http\Requests\Epreuves;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TypeEpreuve;

class CreateEpreuveRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'intitule' => 'required|string|max:200',
      'session' => 'required|string|max:255',
      'url_epreuve' => 'nullable|url',
      'fichier_epreuve' => 'nullable|file|mimes:pdf,doc,docx,odt,rtf,txt|max:2048',
      'type_epreuve' => 'required|in:' . implode(',', TypeEpreuve::values()),
      'duree_en_minute' => 'required|integer|min:1',
      'est_actif' => 'sometimes|boolean',
    ];
  }

  public function messages(): array
  {
    return [
      'intitule.required' => 'L\'intitulé de l\'épreuve est obligatoire',
      'intitule.max' => 'L\'intitulé ne peut pas dépasser 200 caractères',
      'session.required' => 'La session est obligatoire',
      'url_epreuve.url' => 'L\'URL de l\'épreuve doit être valide',
      'type_epreuve.required' => 'Le type d\'épreuve est obligatoire',
      'type_epreuve.in' => 'Le type d\'épreuve n\'est pas valide',
      'duree_en_minute.required' => 'La durée est obligatoire',
      'duree_en_minute.integer' => 'La durée doit être un nombre entier',
      'duree_en_minute.min' => 'La durée doit être d\'au moins 1 minute',
    ];
  }
}
