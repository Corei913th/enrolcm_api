<?php

namespace App\Services\Domain\Auth;

use App\Models\Utilisateur;
use App\Enums\TypeUtilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\ChangePasswordDTO;
use App\Services\Domain\User\UserService;
use App\Services\Domain\User\TokenService;
use App\Traits\HasActivityLogger;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Models\Role;


class AuthService
{
  use HasActivityLogger;

  public function __construct(
    private readonly UserService $userService,
    private readonly TokenService $tokenService,
    ActivityLoggerService $logger
  ) {
    $this->logger = $logger;
  }

  /**
   * Authentifier un utilisateur (hors candidat).
   */
  public function login(LoginDTO $dto): array
  {
    $utilisateur = verifyStaffCredentials($dto->email, $dto->mot_de_passe);

    $accessToken = $this->tokenService->generateToken($utilisateur, 'auth_token', 60);
    $refreshToken = $this->tokenService->generateRefreshToken($utilisateur, 30);

    $this->logOperation('login', 'utilisateur', $utilisateur->id, ['type' => $utilisateur->type_utilisateur->value]);

    return [
      'user' => $utilisateur,
      'access_token' => $accessToken['access_token'],
      'refresh_token' => $refreshToken['refresh_token'],
      'token_type' => $accessToken['token_type'],
      'expires_in' => $accessToken['expires_in'],
    ];
  }

  public function authenticateCandidate(LoginDTO $dto): array
  {
    $utilisateur = verifyCandidateCredentials($dto->email, $dto->mot_de_passe);

    $accessToken = $this->tokenService->generateToken($utilisateur, 'auth_token', 60);
    $refreshToken = $this->tokenService->generateRefreshToken($utilisateur, 30);

    $this->logOperation('login', 'candidat', $utilisateur->id);

    return [
      'user' => $utilisateur,
      'access_token' => $accessToken['access_token'],
      'refresh_token' => $refreshToken['refresh_token'],
      'token_type' => $accessToken['token_type'],
      'expires_in' => $accessToken['expires_in'],
    ];
  }


  /**
   * Rafraîchir le token d'accès.
   */
  public function refreshToken(string $refreshToken): array
  {
    return $this->tokenService->refreshAccessToken($refreshToken);
  }

  /**
   * Déconnecter un utilisateur (supprimer le token courant).
   */
  public function logout(Utilisateur $utilisateur): void
  {
    $token = $utilisateur->currentAccessToken();
    if ($token) {
      $this->tokenService->revokeToken($utilisateur, $token->id);
    }
    $this->logOperation('logout', 'utilisateur', $utilisateur->id);
  }

  /**
   * Déconnecter un utilisateur de tous les appareils.
   */
  public function logoutAll(Utilisateur $utilisateur): void
  {
    $this->tokenService->revokeAllTokens($utilisateur);
    $this->logOperation('logout_all', 'utilisateur', $utilisateur->id);
  }



  /**
   * Changer le mot de passe de l'utilisateur.
   */
  public function changePassword(Utilisateur $utilisateur, ChangePasswordDTO $dto): void
  {
    if (!Hash::check($dto->old_password, $utilisateur->mot_de_passe)) {
      throw ValidationException::withMessages([
        'old_password' => ['L\'ancien mot de passe est incorrect.'],
      ]);
    }

    $utilisateur->update([
      'mot_de_passe' => Hash::make($dto->new_password),
    ]);

    $currentToken = $utilisateur->currentAccessToken();
    $utilisateur->tokens()->where('id', '!=', $currentToken->id)->delete();

    $this->logOperation('change_password', 'utilisateur', $utilisateur->id);
  }

  /**
   * Réinitialiser le mot de passe (sans ancien mot de passe).
   */
  public function resetPassword(string $user_name, string $newPassword): void
  {
    $utilisateur = Utilisateur::where('user_name', $user_name)->firstOrFail();

    $utilisateur->update([
      'mot_de_passe' => Hash::make($newPassword),
    ]);

    $utilisateur->tokens()->delete();
  }

  /**
   * Activer ou désactiver un compte utilisateur.
   */
  public function toggleAccountStatus(Utilisateur $utilisateur, bool $status): void
  {
    $utilisateur->update(['est_actif' => $status]);

    if (!$status) {
      $utilisateur->tokens()->delete();
    }
  }

  /**
   * Obtenir les relations à charger selon le type d'utilisateur.
   */
  private function getRelationsForUser(Utilisateur $utilisateur): array
  {
    $relations = ['roles.permissions'];

    switch ($utilisateur->type_utilisateur) {
      case TypeUtilisateur::ADMIN:
        $relations[] = 'admin';
        break;
      case TypeUtilisateur::CANDIDAT:
        $relations[] = 'candidat';
        break;
      case TypeUtilisateur::CORRECTEUR:
        $relations[] = 'correcteur';
        break;
      case TypeUtilisateur::RESPONSABLE_CENTRE:
        $relations[] = 'responsableCentre';
        break;
    }

    return $relations;
  }

  /**
   * Assigner un rôle par défaut
   */
  private function assignDefaultRole(Utilisateur $utilisateur, string $roleName): void
  {
    $role = Role::where('libelle_role', $roleName)->first();

    if ($role) {
      $utilisateur->roles()->attach($role->id);
    }
  }

  /**
   * Obtenir les informations de l'utilisateur connecté.
   */
  public function getCurrentUser(Utilisateur $utilisateur): Utilisateur
  {
    return $utilisateur->load($this->getRelationsForUser($utilisateur));
  }

  /**
   * Vérifier si un user_name existe déjà.
   */
  public function user_nameExists(string $user_name): bool
  {
    return Utilisateur::where('user_name', $user_name)->exists();
  }

  /**
   * Générer un code de vérification aléatoire (6 chiffres).
   */
  public function generateVerificationCode(): string
  {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
  }

  /**
   * Vérifier l'email d'un utilisateur.
   *
   * @param string $id ID de l'utilisateur
   * @param string $hash Hash de vérification
   * @return array Résultat de la vérification
   */
  public function verifyEmail(string $id, string $hash): array
  {
    try {
      $utilisateur = Utilisateur::findOrFail($id);

      if ($utilisateur->hasVerifiedEmail()) {
        return [
          'success' => false,
          'message' => 'Email déjà vérifié'
        ];
      }

      // Vérifier le hash
      $expectedHash = sha1($utilisateur->getEmailForVerification());
      if (!hash_equals($expectedHash, $hash)) {
        return [
          'success' => false,
          'message' => 'Lien de vérification invalide'
        ];
      }

      // Marquer l'email comme vérifié
      $utilisateur->markEmailAsVerified();

      return [
        'success' => true,
        'message' => 'Email vérifié avec succès'
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'message' => 'Erreur lors de la vérification: ' . $e->getMessage()
      ];
    }
  }
}
