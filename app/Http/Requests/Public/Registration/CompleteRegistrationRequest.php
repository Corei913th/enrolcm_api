<?php

namespace App\Http\Requests\Public\Registration;

use Illuminate\Foundation\Http\FormRequest;

class CompleteRegistrationRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'token_temporaire' => [
        'required',
        'string',
      ],
      'email' => [
        'required',
        'email',
        'max:255',
        'unique:utilisateurs,email',
      ],
      'telephone' => [
        'required',
        'string',
        'regex:/^\+?[0-9]{9,15}$/',
      ],
      'password' => [
        'required',
        'string',
        'min:8',
        'confirmed',
      ],
      'password_confirmation' => [
        'required',
        'string',
      ],
    ];
  }

  public function messages(): array
  {
    return [
      'token_temporaire.required' => 'Le token temporaire est obligatoire',
      'token_temporaire.string' => 'Le token temporaire doit être une chaîne de caractères',

      'email.required' => 'L\'adresse email est obligatoire',
      'email.email' => 'L\'adresse email doit être valide',
      'email.max' => 'L\'adresse email ne doit pas dépasser 255 caractères',
      'email.unique' => 'Cette adresse email est déjà utilisée',

      'telephone.required' => 'Le numéro de téléphone est obligatoire',
      'telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères',
      'telephone.regex' => 'Le numéro de téléphone doit être valide (9 à 15 chiffres)',

      'password.required' => 'Le mot de passe est obligatoire',
      'password.string' => 'Le mot de passe doit être une chaîne de caractères',
      'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
      'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',

      'password_confirmation.required' => 'La confirmation du mot de passe est obligatoire',
    ];
  }
}
