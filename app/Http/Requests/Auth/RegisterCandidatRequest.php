<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCandidatRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_name' => 'required|string|unique:utilisateurs,user_name', // Numéro de reçu
            'mot_de_passe' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'user_name.required' => 'Le numéro de reçu est obligatoire',
            'user_name.unique' => 'Ce numéro de reçu a déjà été utilisé',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 6 caractères',
            'mot_de_passe.confirmed' => 'Les mots de passe ne correspondent pas',
        ];
    }
}
