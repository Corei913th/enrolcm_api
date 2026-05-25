<?php

namespace App\Http\Requests\Admin\Users;

use App\Enums\TypeUtilisateur;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'sometimes|email|unique:utilisateurs,email',
            'user_name' => 'required|string|unique:utilisateurs,user_name',
            'mot_de_passe' => 'required|string|min:6|confirmed',
            'telephone' => [
                'sometimes',
                'required',
                'string',
                'regex:/^(6[5-9]\d{7}|2[2-3]\d{7})$/',
                Rule::unique('utilisateurs', 'telephone'),
            ],
            'type_utilisateur' => 'required|in:' . implode(',', TypeUtilisateur::values()),

            // Champs spécifiques Admin
            'matricule' => 'required_if:type_utilisateur,' . TypeUtilisateur::ADMIN->value . '|nullable|string|max:50',

            // Champs spécifiques Correcteur
            'specialite' => 'required_if:type_utilisateur,' . TypeUtilisateur::CORRECTEUR->value . '|nullable|string|max:100',
            'matricule_enseignant' => 'required_if:type_utilisateur,' . TypeUtilisateur::CORRECTEUR->value . '|nullable|string|max:50',

            // Champs spécifiques Responsable Centre
            'code_agent' => 'required_if:type_utilisateur,' . TypeUtilisateur::RESPONSABLE_CENTRE->value . '|nullable|string|max:50',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'L\'adresse email est obligatoire',
            'email.unique' => 'Cette adresse email est déjà utilisée',
            'user_name.required' => 'Le nom d\'utilisateur est obligatoire',
            'user_name.unique' => 'Ce nom d\'utilisateur est déjà utilisé',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 6 caractères',
            'mot_de_passe.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'type_utilisateur.required' => 'Le type d\'utilisateur est obligatoire',
            'matricule.required_if' => 'Le matricule est obligatoire pour un admin',
            'specialite.required_if' => 'La spécialité est obligatoire pour un correcteur',
            'matricule_enseignant.required_if' => 'Le matricule enseignant est obligatoire pour un correcteur',
            'code_agent.required_if' => 'Le code agent est obligatoire pour un responsable de centre',
        ];
    }
}
