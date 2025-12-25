<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\StatutVerificationPaiement;
use Illuminate\Validation\Rule;

class VerifyReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Géré par le middleware
    }

    public function rules(): array
    {
        return [
            'statut' => [
                'required',
                Rule::in([
                    StatutVerificationPaiement::VERIFIE->value,
                    StatutVerificationPaiement::REJETE->value,
                ]),
            ],
            'motif_rejet' => [
                'required_if:statut,' . StatutVerificationPaiement::REJETE->value,
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'statut.required' => 'Le statut est obligatoire',
            'statut.in' => 'Le statut doit être "verifie" ou "rejete"',
            'motif_rejet.required_if' => 'Le motif de rejet est obligatoire',
            'motif_rejet.max' => 'Le motif ne doit pas dépasser 500 caractères',
        ];
    }
}
