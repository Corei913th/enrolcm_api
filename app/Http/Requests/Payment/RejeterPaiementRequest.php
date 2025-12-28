<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class RejeterPaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'motif_rejet' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'motif_rejet.required' => 'Le motif de rejet est obligatoire',
            'motif_rejet.string' => 'Le motif de rejet doit être une chaîne de caractères',
            'motif_rejet.min' => 'Le motif de rejet doit contenir au moins 10 caractères',
            'motif_rejet.max' => 'Le motif de rejet ne peut pas dépasser 1000 caractères',
        ];
    }
}
