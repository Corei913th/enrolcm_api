<?php

namespace App\Helpers;

use App\Models\Utilisateur;
use App\Enums\TypeUtilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthHelper
{
  /**
   * Trouver et vérifier les credentials d'un utilisateur
   * 
   * @param string $identifier Email ou username
   * @param string $password Mot de passe
   * @param string $identifierField Champ à utiliser pour la recherche ('email' ou 'user_name')
   * @param TypeUtilisateur|null $excludeType Type d'utilisateur à exclure
   * @return Utilisateur
   * @throws ValidationException
   */
  public static function findAndVerifyUser(
    string $identifier,
    string $password,
    string $identifierField = 'email',
    ?TypeUtilisateur $excludeType = null
  ): Utilisateur {
    $query = Utilisateur::where($identifierField, $identifier);

    if ($excludeType) {
      $query->where('type_utilisateur', '!=', $excludeType);
    }

    $utilisateur = $query->first();

    if (!$utilisateur || !Hash::check($password, $utilisateur->mot_de_passe)) {
      throw ValidationException::withMessages([
        'email' => ['Les identifiants fournis sont incorrects.'],
      ]);
    }

    if (!$utilisateur->est_actif) {
      throw ValidationException::withMessages([
        'email' => ['Votre compte est désactivé. Veuillez contacter l\'administrateur.'],
      ]);
    }

    return $utilisateur;
  }

  /**
   * Vérifier les credentials d'un candidat
   * 
   * @param string $email Email du candidat
   * @param string $password Mot de passe
   * @return Utilisateur
   * @throws ValidationException
   */
  public static function verifyCandidateCredentials(string $email, string $password): Utilisateur
  {
    $utilisateur = Utilisateur::where('email', $email)
      ->where('type_utilisateur', TypeUtilisateur::CANDIDAT)
      ->first();

    if (!$utilisateur || !Hash::check($password, $utilisateur->mot_de_passe)) {
      throw ValidationException::withMessages([
        'email' => ['Les identifiants fournis sont incorrects.'],
      ]);
    }

    if (!$utilisateur->est_actif) {
      throw ValidationException::withMessages([
        'email' => ['Votre compte est désactivé. Veuillez contacter l\'administrateur.'],
      ]);
    }

    return $utilisateur;
  }

  /**
   * Vérifier les credentials d'un staff (non-candidat)
   * 
   * @param string $username Username
   * @param string $password Mot de passe
   * @return Utilisateur
   * @throws ValidationException
   */
  public static function verifyStaffCredentials(string $username, string $password): Utilisateur
  {
    return self::findAndVerifyUser(
      $username,
      $password,
      'user_name',
      TypeUtilisateur::CANDIDAT
    );
  }
}
