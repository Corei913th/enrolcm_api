<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TypeUtilisateur;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('user') ?? $this->route('id');

        return [
            'email' => [
                'sometimes', 
                'email', 
                Rule::unique('utilisateurs', 'email')->ignore($userId)
            ],
            'user_name' => [
                'sometimes', 
                'string', 
                Rule::unique('utilisateurs', 'user_name')->ignore($userId)
            ],
            'telephone' => [
                'sometimes',
                'string',
                'regex:/^(6[5-9]\d{7}|2[2-3]\d{7})$/',
                Rule::unique('utilisateurs', 'telephone')->ignore($userId)
            ],
            'est_actif' => 'boolean',
            
            // On ne permet généralement pas de changer le type d'utilisateur lors d'un simple update
            // sauf cas spécifique, mais ici je vais le laisser open si besoin, sinon on l'omet.
            // Pour l'instant, on assume qu'on met à jour les infos de base.
            
            // Password update should be a separate endpoint usually, but if included:
            'mot_de_passe' => 'nullable|string|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'email.email' => 'Format d\'email invalide',
            'email.unique' => 'Cet email est déjà utilisé',
            'user_name.unique' => 'Ce nom d\'utilisateur est déjà utilisé',
            'telephone.regex' => 'Format de téléphone invalide (Cameroun)',
            'telephone.unique' => 'Ce numéro de téléphone est déjà associé à un compte',
            'mot_de_passe.min' => 'Le mot de passe doit faire au moins 6 caractères',
            'mot_de_passe.confirmed' => 'La confirmation du mot de passe échoue',
        ];
    }
}
