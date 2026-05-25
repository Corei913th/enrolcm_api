<?php

namespace App\Http\Requests\Candidats;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'preuve_paiement' => [
                'required',
                'file',
                'max:' . (5 * 1024), // 5MB
                'mimes:pdf,jpg,jpeg,png',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'preuve_paiement.required' => 'Le reçu de paiement est obligatoire',
            'preuve_paiement.file' => 'Le fichier doit être valide',
            'preuve_paiement.max' => 'Le fichier ne doit pas dépasser 5 MB',
            'preuve_paiement.mimes' => 'Le fichier doit être au format PDF, JPG, JPEG ou PNG',
        ];
    }
}
