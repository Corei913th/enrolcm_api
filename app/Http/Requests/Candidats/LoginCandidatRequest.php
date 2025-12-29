<?php

namespace App\Http\Requests\Candidats;

use Illuminate\Foundation\Http\FormRequest;

class LoginCandidatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pru' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'pru.required' => 'Le PRU est obligatoire',
            'password.required' => 'Le mot de passe est obligatoire',
        ];
    }
}
