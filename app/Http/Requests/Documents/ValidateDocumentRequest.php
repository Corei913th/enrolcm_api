<?php

namespace App\Http\Requests\Documents;

use Illuminate\Foundation\Http\FormRequest;

class ValidateDocumentRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'statut' => ['required', 'in:VALIDE,REJETE'],
      'commentaire' => ['nullable', 'string', 'max:1000'],
    ];
  }

  public function messages(): array
  {
    return [
      'statut.required' => 'Le statut est obligatoire',
      'statut.in' => 'Le statut doit être VALIDE ou REJETE',
      'commentaire.max' => 'Le commentaire ne doit pas dépasser 1000 caractères',
    ];
  }
}
