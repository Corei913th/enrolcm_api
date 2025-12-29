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
            'reference' => ['required', 'string', 'max:50'],
            'montant' => ['required', 'numeric', 'min:0'],
            'preuve' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'concours_id.required' => 'Le concours est obligatoire',
            'concours_id.uuid' => 'L\'identifiant du concours est invalide',
            'concours_id.exists' => 'Le concours spécifié n\'existe pas',
            'reference.required' => 'La référence de paiement (PRU) est obligatoire',
            'montant.required' => 'Le montant est obligatoire',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être positif',
            'preuve.required' => 'La preuve de paiement est obligatoire',
            'preuve.file' => 'La preuve doit être un fichier',
            'preuve.mimes' => 'La preuve doit être au format JPG, PNG ou PDF',
            'preuve.max' => 'La preuve ne doit pas dépasser 5MB',
        ];
    }
}
