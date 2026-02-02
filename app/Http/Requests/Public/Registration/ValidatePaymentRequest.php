<?php

namespace App\Http\Requests\Public\Registration;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePaymentRequest extends FormRequest
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
      'reference' => [
        'required',
        'string',
        'min:6',
        'max:50',
      ],
      'montant' => [
        'required',
        'numeric',
        'min:0',
      ],
      'banque' => [
        'required',
        'string',
        'max:100',
      ],
      'date_paiement' => [
        'required',
        'date',
        'before_or_equal:today',
      ],
      'numero_compte' => [
        'nullable',
        'string',
        'max:50',
      ],
    ];
  }

  public function messages(): array
  {
    return [
      'concours_id.required' => 'L\'identifiant du concours est obligatoire',
      'concours_id.uuid' => 'L\'identifiant du concours doit être un UUID valide',
      'concours_id.exists' => 'Le concours sélectionné n\'existe pas',

      'reference.required' => 'La référence de paiement (PRU) est obligatoire',
      'reference.string' => 'La référence de paiement doit être une chaîne de caractères',
      'reference.min' => 'La référence de paiement doit contenir au moins 6 caractères',
      'reference.max' => 'La référence de paiement ne doit pas dépasser 50 caractères',

      'montant.required' => 'Le montant du paiement est obligatoire',
      'montant.numeric' => 'Le montant du paiement doit être un nombre',
      'montant.min' => 'Le montant du paiement doit être supérieur ou égal à 0',

      'banque.required' => 'Le nom de la banque est obligatoire',
      'banque.string' => 'Le nom de la banque doit être une chaîne de caractères',
      'banque.max' => 'Le nom de la banque ne doit pas dépasser 100 caractères',

      'date_paiement.required' => 'La date de paiement est obligatoire',
      'date_paiement.date' => 'La date de paiement doit être une date valide',
      'date_paiement.before_or_equal' => 'La date de paiement ne peut pas être dans le futur',

      'numero_compte.string' => 'Le numéro de compte doit être une chaîne de caractères',
      'numero_compte.max' => 'Le numéro de compte ne doit pas dépasser 50 caractères',
    ];
  }
}
