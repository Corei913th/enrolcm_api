<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCentreStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'est_actif' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'est_actif.required' => 'Le statut actif est obligatoire',
            'est_actif.boolean' => 'Le statut actif doit être un booléen',
        ];
    }
}
