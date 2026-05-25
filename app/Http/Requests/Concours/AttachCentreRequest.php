<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

class AttachCentreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'centre_id' => 'required|uuid|exists:centres,id',
            'est_actif' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'centre_id.required' => 'L\'ID du centre est obligatoire',
            'centre_id.uuid' => 'L\'ID du centre doit être un UUID valide',
            'centre_id.exists' => 'Le centre spécifié n\'existe pas',
            'est_actif.boolean' => 'Le statut actif doit être un booléen',
        ];
    }
}
