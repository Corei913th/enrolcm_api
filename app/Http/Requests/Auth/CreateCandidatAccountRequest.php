<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class CreateCandidatAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

     public function rules()
    {
        return [
            'user_name' => 'required|string|unique:utilisateurs,user_name',
            'mot_de_passe' => 'required|string|min:6|confirmed',
            'nationalite_cand' => 'nullable|string|max:50',
        ];
    }

    public function messages()
    {
        return [
            'user_name.required' => 'Le nom d\'utilisateur est obligatoire',
            'user_name.string' => 'Le nom d\'utilisateur doit être une chaîne de caractères',      
            'mot_de_passe.string' => 'Le mot de passe doit être une chaîne de caractères',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 6 caractères',
        ];
    }
}