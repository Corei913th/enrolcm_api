<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurerPaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Informations bancaires de base
            'banque_nom' => ['required', 'string', 'max:100'],
            'numero_compte' => ['required', 'string', 'max:50'],
            'nom_beneficiaire' => ['required', 'string', 'max:200'],

            // Informations bancaires complètes
            'devise' => ['sometimes', 'string', 'size:3', 'in:XAF,USD,EUR'],
            'code_banque' => ['nullable', 'string', 'max:11'],
            'agence_banque' => ['nullable', 'string', 'max:100'],
            'iban' => ['nullable', 'string', 'max:34'],

            // Configuration paiement
            'type_paiement' => ['sometimes', 'string', 'in:virement,cheque,mobile_money,especes,carte_bancaire'],
            'banques_acceptees' => ['nullable', 'array'],
            'banques_acceptees.*' => ['string', 'max:100'],
            'frais_paiement' => ['sometimes', 'numeric', 'min:0', 'max:999999.99'],

            // Montant et date
            'montant' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'date_limite' => ['required', 'date', 'after:today'],

            // Validation et sécurité
            'reference_format' => ['nullable', 'string', 'max:255'],
            'minimum_confiance_ocr' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'validation_auto' => ['sometimes', 'boolean'],

            // Instructions et métadonnées
            'instructions' => ['nullable', 'string', 'max:2000'],
            'commentaires' => ['nullable', 'string', 'max:1000'],
            'est_actif' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // Informations bancaires de base
            'banque_nom.required' => 'Le nom de la banque est obligatoire',
            'banque_nom.max' => 'Le nom de la banque ne peut pas dépasser 100 caractères',
            'numero_compte.required' => 'Le numéro de compte est obligatoire',
            'numero_compte.max' => 'Le numéro de compte ne peut pas dépasser 50 caractères',
            'nom_beneficiaire.required' => 'Le nom du bénéficiaire est obligatoire',
            'nom_beneficiaire.max' => 'Le nom du bénéficiaire ne peut pas dépasser 200 caractères',

            // Informations bancaires complètes
            'devise.size' => 'La devise doit être composée de 3 caractères',
            'devise.in' => 'La devise doit être XAF, USD ou EUR',
            'code_banque.max' => 'Le code de la banque ne peut pas dépasser 11 caractères',
            'agence_banque.max' => 'Le nom de l\'agence ne peut pas dépasser 100 caractères',
            'iban.max' => 'L\'IBAN ne peut pas dépasser 34 caractères',

            // Configuration paiement
            'type_paiement.in' => 'Le type de paiement doit être virement, chèque, mobile_money, espèces ou carte_bancaire',
            'banques_acceptees.array' => 'Les banques acceptées doivent être une liste',
            'banques_acceptees.*.max' => 'Chaque nom de banque ne peut pas dépasser 100 caractères',
            'frais_paiement.numeric' => 'Les frais de paiement doivent être un nombre',
            'frais_paiement.min' => 'Les frais de paiement doivent être positifs',
            'frais_paiement.max' => 'Les frais de paiement sont trop élevés',

            // Montant et date
            'montant.required' => 'Le montant est obligatoire',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être positif',
            'montant.max' => 'Le montant est trop élevé',
            'date_limite.required' => 'La date limite est obligatoire',
            'date_limite.date' => 'La date limite doit être une date valide',
            'date_limite.after' => 'La date limite doit être dans le futur',

            // Validation et sécurité
            'reference_format.max' => 'Le format de référence ne peut pas dépasser 255 caractères',
            'minimum_confiance_ocr.numeric' => 'La confiance OCR minimale doit être un nombre',
            'minimum_confiance_ocr.min' => 'La confiance OCR minimale doit être positive',
            'minimum_confiance_ocr.max' => 'La confiance OCR minimale ne peut pas dépasser 100%',
            'validation_auto.boolean' => 'La validation automatique doit être vrai ou faux',

            // Instructions et métadonnées
            'instructions.max' => 'Les instructions ne peuvent pas dépasser 2000 caractères',
            'commentaires.max' => 'Les commentaires ne peuvent pas dépasser 1000 caractères',
            'est_actif.boolean' => 'Le statut actif doit être vrai ou faux',
        ];
    }
}
