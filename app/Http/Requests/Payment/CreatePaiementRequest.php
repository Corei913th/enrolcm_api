<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'concours_id' => ['required', 'uuid', 'exists:concours,id'],
            'reference' => ['required', 'string', 'max:50', 'exists:payment_references,reference'],
            'montant' => ['required', 'numeric', 'min:0'],
            'preuve_paiement' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'concours_id.required' => 'Le concours est obligatoire',
            'concours_id.uuid' => 'L\'identifiant du concours est invalide',
            'concours_id.exists' => 'Le concours spécifié n\'existe pas',
            'reference.required' => 'La référence de paiement est obligatoire',
            'reference.exists' => 'La référence de paiement est invalide',
            'montant.required' => 'Le montant est obligatoire',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être positif',
            'preuve_paiement.required' => 'La preuve de paiement est obligatoire',
            'preuve_paiement.file' => 'La preuve doit être un fichier',
            'preuve_paiement.mimes' => 'La preuve doit être au format JPG, PNG ou PDF',
            'preuve_paiement.max' => 'La preuve ne doit pas dépasser 5MB',
        ];
    }
}
