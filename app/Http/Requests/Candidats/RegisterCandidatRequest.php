<?php

namespace App\Http\Requests\Candidats;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterCandidatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pru' => ['required', 'string'],
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:utilisateurs,email'],
            'telephone' => [
                'sometimes',
                'required',
                'string',
                'regex:/^(6[5-9]\d{7}|2[2-3]\d{7})$/',
                Rule::unique('candidats', 'telephone_candidat'),
                Rule::unique('utilisateurs', 'telephone'),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'concours_id' => ['required', 'uuid', 'exists:concours,id'],
            'session_id' => ['required', 'uuid', 'exists:sessions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'pru.required' => 'Le PRU est obligatoire',
            'nom.required' => 'Le nom est obligatoire',
            'prenom.required' => 'Le prénom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être valide',
            'email.unique' => 'Cet email est déjà utilisé',
            'telephone.required' => 'Le téléphone est obligatoire',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'concours_id.required' => 'Le concours est obligatoire',
            'concours_id.exists' => 'Le concours spécifié n\'existe pas',
            'session_id.required' => 'La session est obligatoire',
            'session_id.exists' => 'La session spécifiée n\'existe pas',
        ];
    }
}
