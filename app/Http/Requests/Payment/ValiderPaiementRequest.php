<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class ValiderPaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'commentaire' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'commentaire.string' => 'Le commentaire doit être une chaîne de caractères',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 500 caractères',
        ];
    }
}
