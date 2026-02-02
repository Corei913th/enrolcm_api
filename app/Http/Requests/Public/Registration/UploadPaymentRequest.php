<?php

namespace App\Http\Requests\Public\Registration;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentRequest extends FormRequest
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
      'session_id' => [
        'required',
        'uuid',
        'exists:sessions,id',
      ],
      'preuve_paiement' => [
        'required',
        'file',
        'mimes:jpg,jpeg,png,pdf',
        'max:5120', // 5MB
      ],
      // Données de paiement (optionnelles si OCR réussit)
      'reference_paiement' => [
        'nullable',
        'string',
        'min:6',
        'max:50',
      ],
      'montant' => [
        'nullable',
        'numeric',
        'min:0',
      ],
      'date_paiement' => [
        'nullable',
        'date',
        'before_or_equal:today',
      ],
      'numero_compte' => [
        'nullable',
        'string',
        'max:50',
      ],
      // Données d'éligibilité (stockées pour étape 3)
      'eligibility_data' => [
        'required',
        'array',
      ],
      'eligibility_data.nom' => [
        'required',
        'string',
        'max:100',
      ],
      'eligibility_data.prenom' => [
        'required',
        'string',
        'max:100',
      ],
      'eligibility_data.date_naissance' => [
        'required',
        'date',
        'before:today',
      ],
      'eligibility_data.serie_bac' => [
        'required',
        'string',
        'max:10',
      ],
      'eligibility_data.annee_bac' => [
        'required',
        'integer',
        'min:1950',
        'max:' . date('Y'),
      ],
      'eligibility_data.nationalite' => [
        'nullable',
        'string',
        'max:100',
      ],
      'eligibility_data.filiere_id' => [
        'nullable',
        'uuid',
        'exists:filieres,id',
      ],
    ];
  }

  public function messages(): array
  {
    return [
      'concours_id.required' => 'L\'identifiant du concours est obligatoire',
      'concours_id.uuid' => 'L\'identifiant du concours doit être un UUID valide',
      'concours_id.exists' => 'Le concours sélectionné n\'existe pas',

      'session_id.required' => 'L\'identifiant de la session est obligatoire',
      'session_id.uuid' => 'L\'identifiant de la session doit être un UUID valide',
      'session_id.exists' => 'La session sélectionnée n\'existe pas',

      'preuve_paiement.required' => 'La preuve de paiement est obligatoire',
      'preuve_paiement.file' => 'La preuve de paiement doit être un fichier',
      'preuve_paiement.mimes' => 'La preuve de paiement doit être au format JPG, JPEG, PNG ou PDF',
      'preuve_paiement.max' => 'La preuve de paiement ne doit pas dépasser 5MB',

      'reference_paiement.min' => 'La référence de paiement doit contenir au moins 6 caractères',
      'reference_paiement.max' => 'La référence de paiement ne doit pas dépasser 50 caractères',

      'montant.numeric' => 'Le montant doit être un nombre',
      'montant.min' => 'Le montant doit être supérieur ou égal à 0',

      'date_paiement.date' => 'La date de paiement doit être une date valide',
      'date_paiement.before_or_equal' => 'La date de paiement ne peut pas être dans le futur',

      'numero_compte.string' => 'Le numéro de compte doit être une chaîne de caractères',
      'numero_compte.max' => 'Le numéro de compte ne doit pas dépasser 50 caractères',

      'eligibility_data.required' => 'Les données d\'éligibilité sont obligatoires',
      'eligibility_data.array' => 'Les données d\'éligibilité doivent être un tableau',

      'eligibility_data.nom.required' => 'Le nom est obligatoire',
      'eligibility_data.prenom.required' => 'Le prénom est obligatoire',
      'eligibility_data.date_naissance.required' => 'La date de naissance est obligatoire',
      'eligibility_data.serie_bac.required' => 'La série du baccalauréat est obligatoire',
      'eligibility_data.annee_bac.required' => 'L\'année d\'obtention du baccalauréat est obligatoire',
    ];
  }
}
