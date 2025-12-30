<?php

namespace App\Http\Requests\Candidats;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPRURequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pru' => ['required', 'string'],
            'concours_id' => ['required', 'uuid', 'exists:concours,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'pru.required' => 'Le PRU est obligatoire',
            'concours_id.required' => 'L\'identifiant du concours est obligatoire',
            'concours_id.exists' => 'Le concours spécifié n\'existe pas',
        ];
    }
}
