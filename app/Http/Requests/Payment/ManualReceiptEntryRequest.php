<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class ManualReceiptEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero_recu' => 'required|string|unique:payment_receipts,numero_recu',
            'receipt_image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'montant' => 'required|numeric|min:0',
            'banque' => 'nullable|string|max:100',
            'date_paiement' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'numero_recu.required' => 'Le numéro de reçu est obligatoire',
            'numero_recu.unique' => 'Ce numéro de reçu a déjà été utilisé',
            'receipt_image.required' => 'L\'image du reçu est obligatoire',
            'receipt_image.image' => 'Le fichier doit être une image',
            'receipt_image.mimes' => 'L\'image doit être au format JPG ou PNG',
            'receipt_image.max' => 'L\'image ne doit pas dépasser 5MB',
            'montant.required' => 'Le montant est obligatoire',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être positif',
            'date_paiement.date' => 'La date de paiement doit être une date valide',
        ];
    }
}
