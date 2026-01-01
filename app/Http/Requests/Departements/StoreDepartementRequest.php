<?php

namespace App\Http\Requests\Departements;

use Illuminate\Foundation\Http\FormRequest;


class StoreDepartementRequest extends FormRequest
{

  public function authorize(): bool
  {
    return true;
  }


  public function rules(): array
  {
    return [
      'code_departement' => [
        'required',
        'string',
        'max:10',
        'regex:/^[A-Z0-9_-]+$/',
      ],
      'libelle_departement' => [
        'required',
        'string',
        'max:200',
        'min:3',
      ],
      'ecole_id' => [
        'required',
        'uuid',
        'exists:ecoles,id',
      ],
      'desc_departement' => [
        'nullable',
        'string',
        'max:1000',
      ],
      'est_actif' => [
        'sometimes',
        'boolean',
      ],
    ];
  }


  public function messages(): array
  {
    return [
      'code_departement.required' => 'Le code du département est obligatoire.',
      'code_departement.regex' => 'Le code doit contenir uniquement des lettres majuscules, chiffres, tirets et underscores.',
      'libelle_departement.required' => 'Le libellé du département est obligatoire.',
      'libelle_departement.min' => 'Le libellé doit contenir au moins 3 caractères.',
      'ecole_id.required' => 'L\'école est obligatoire.',
      'ecole_id.exists' => 'L\'école spécifiée n\'existe pas.',
      'desc_departement.max' => 'La description ne peut pas dépasser 1000 caractères.',
    ];
  }


  public function attributes(): array
  {
    return [
      'code_departement' => 'code du département',
      'libelle_departement' => 'libellé du département',
      'ecole_id' => 'école',
      'desc_departement' => 'description',
      'est_actif' => 'statut actif',
    ];
  }
}
