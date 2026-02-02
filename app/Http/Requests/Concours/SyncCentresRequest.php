<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

class SyncCentresRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation(): void
  {
    // Filtrer les valeurs null du tableau centre_ids
    if ($this->has('centre_ids') && is_array($this->centre_ids)) {
      $this->merge([
        'centre_ids' => array_values(array_filter($this->centre_ids, fn($id) => !is_null($id)))
      ]);
    }
  }

  public function rules(): array
  {
    return [
      'centre_ids' => 'required|array',
      'centre_ids.*' => 'uuid|exists:centres,id',
    ];
  }

  public function messages(): array
  {
    return [
      'centre_ids.required' => 'La liste des centres est obligatoire',
      'centre_ids.array' => 'La liste des centres doit être un tableau',
      'centre_ids.*.uuid' => 'Chaque ID de centre doit être un UUID valide',
      'centre_ids.*.exists' => 'Un ou plusieurs centres n\'existent pas',
    ];
  }
}
