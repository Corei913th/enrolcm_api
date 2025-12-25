<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CheckReceiptNumberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero_recu' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'numero_recu.required' => 'Le numéro de reçu est obligatoire',
            'numero_recu.string' => 'Le numéro de reçu doit être une chaîne de caractères',
        ];
    }
}
