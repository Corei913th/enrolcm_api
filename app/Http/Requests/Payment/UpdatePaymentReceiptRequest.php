<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'candidat_id' => ['sometimes', 'uuid', 'exists:candidats,utilisateur_id'],
            'numero_recu' => ['sometimes', 'string', 'max:255'],
            'banque' => ['sometimes', 'nullable', 'string', 'max:255'],
            'montant' => ['sometimes', 'numeric', 'min:0', 'max:9999999.99'],
            'date_paiement' => ['sometimes', 'nullable', 'date', 'before_or_equal:today'],
            'image_path' => ['sometimes', 'string'],
            'ocr_data' => ['sometimes', 'nullable', 'array'],
            'statut_verification' => ['sometimes', 'in:en_attente,verifie,rejete'],
            'motif_rejet' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'candidat_id.uuid' => 'L\'identifiant du candidat doit être un UUID valide',
            'candidat_id.exists' => 'Le candidat spécifié n\'existe pas',
            'numero_recu.string' => 'Le numéro de reçu doit être une chaîne de caractères',
            'numero_recu.max' => 'Le numéro de reçu ne peut pas dépasser 255 caractères',
            'banque.string' => 'Le nom de la banque doit être une chaîne de caractères',
            'banque.max' => 'Le nom de la banque ne peut pas dépasser 255 caractères',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être positif',
            'montant.max' => 'Le montant est trop élevé',
            'date_paiement.date' => 'La date de paiement doit être une date valide',
            'date_paiement.before_or_equal' => 'La date de paiement ne peut pas être dans le futur',
            'statut_verification.in' => 'Le statut de vérification doit être: en_attente, verifie ou rejete',
            'motif_rejet.string' => 'Le motif de rejet doit être une chaîne de caractères',
            'motif_rejet.max' => 'Le motif de rejet ne peut pas dépasser 1000 caractères',
        ];
    }
}
