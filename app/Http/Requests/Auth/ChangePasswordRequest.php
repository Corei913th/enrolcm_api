<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed|different:old_password',
        ];
    }

    public function messages()
    {
        return [
            'old_password.required' => 'L\'ancien mot de passe est obligatoire',
            'new_password.required' => 'Le nouveau mot de passe est obligatoire',
            'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères',
            'new_password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas',
            'new_password.different' => 'Le nouveau mot de passe doit être différent de l\'ancien',
        ];
    }
}
