<?php

namespace App\Http\Requests\Public\Registration;

use Illuminate\Foundation\Http\FormRequest;

class CheckEligibilityRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'concours_id' => [
        'required',
        'uuid',
        'exists:concours,id',
      ],
      'date_naissance' => [
        'required',
        'date',
        'before:today',
      ],
      'serie_bac' => [
        'required',
        'string',
        'max:10',
      ],
      'nationalite' => [
        'required',
        'string',
        'max:100',
      ],
    ];
  }

  public function messages(): array
  {
    return [
      'concours_id.required' => 'L\'identifiant du concours est obligatoire',
      'concours_id.uuid' => 'L\'identifiant du concours doit être un UUID valide',
      'concours_id.exists' => 'Le concours sélectionné n\'existe pas',

      'date_naissance.required' => 'La date de naissance est obligatoire',
      'date_naissance.date' => 'La date de naissance doit être une date valide',
      'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui',

      'serie_bac.required' => 'La série du baccalauréat est obligatoire',
      'serie_bac.string' => 'La série du baccalauréat doit être une chaîne de caractères',
      'serie_bac.max' => 'La série du baccalauréat ne doit pas dépasser 10 caractères',

      'nationalite.required' => 'La nationalité est obligatoire',
      'nationalite.string' => 'La nationalité doit être une chaîne de caractères',
      'nationalite.max' => 'La nationalité ne doit pas dépasser 100 caractères',
    ];
  }
}
