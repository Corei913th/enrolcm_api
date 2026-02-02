<?php

namespace App\Services\Domain\User;

use App\Constants\TokenConstants;
use App\Models\Utilisateur;
use App\Traits\HasActivityLogger;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Laravel\Sanctum\PersonalAccessToken;

class TokenService
{
  use HasActivityLogger;

  public function __construct(ActivityLoggerService $logger)
  {
    $this->logger = $logger;
  }
  /**
   * Générer un token d'authentification avec expiration pour un utilisateur.
   */
  public function generateToken(
    Utilisateur $utilisateur,
    string $tokenName = TokenConstants::DEFAULT_ACCESS_TOKEN_NAME,
    int $expiresInMinutes = TokenConstants::DEFAULT_ACCESS_TOKEN_EXPIRY_MINUTES
  ): array {
    $utilisateur->tokens()->where('name', $tokenName)->delete();

    $expiresAt = now()->addMinutes($expiresInMinutes);
    $token = $utilisateur->createToken($tokenName, [TokenConstants::ABILITY_ALL], $expiresAt);

    $this->logOperation('generate_token', 'utilisateur', $utilisateur->id, ['token_name' => $tokenName]);

    return [
      'access_token' => $token->plainTextToken,
      'token_type' => TokenConstants::TOKEN_TYPE,
      'expires_at' => $expiresAt->toIso8601String(),
      'expires_in' => $expiresInMinutes * 60,
    ];
  }

  /**
   * Générer un refresh token pour un utilisateur.
   */
  public function generateRefreshToken(
    Utilisateur $utilisateur,
    int $expiresInDays = TokenConstants::DEFAULT_REFRESH_TOKEN_EXPIRY_DAYS
  ): array {
    $utilisateur->tokens()->where('name', TokenConstants::DEFAULT_REFRESH_TOKEN_NAME)->delete();

    $expiresAt = now()->addDays($expiresInDays);
    $token = $utilisateur->createToken(
      TokenConstants::DEFAULT_REFRESH_TOKEN_NAME,
      [TokenConstants::ABILITY_REFRESH],
      $expiresAt
    );

    return [
      'refresh_token' => $token->plainTextToken,
      'expires_at' => $expiresAt->toIso8601String(),
      'expires_in' => $expiresInDays * 24 * 60 * 60,
    ];
  }

  /**
   * Rafraîchir un access token à partir d'un refresh token.
   */
  public function refreshAccessToken(string $refreshToken): array
  {
    [$id, $token] = explode('|', $refreshToken, 2);

    $tokenModel = PersonalAccessToken::find($id);

    if (!$tokenModel || !hash_equals($tokenModel->token, hash('sha256', $token))) {
      throw new \Exception('Refresh token invalide');
    }

    if ($tokenModel->name !== TokenConstants::DEFAULT_REFRESH_TOKEN_NAME) {
      throw new \Exception('Ce token n\'est pas un refresh token');
    }

    if ($tokenModel->expires_at && $tokenModel->expires_at->isPast()) {
      throw new \Exception('Refresh token expiré');
    }

    $utilisateur = $tokenModel->tokenable;

    if (!$utilisateur->est_actif) {
      throw new \Exception('Utilisateur désactivé');
    }

    $accessToken = $this->generateToken($utilisateur);
    $newRefreshToken = $this->generateRefreshToken($utilisateur);

    return array_merge($accessToken, $newRefreshToken);
  }

  /**
   * Révoquer tous les tokens d'un utilisateur (logout).
   */
  public function revokeAllTokens(Utilisateur $utilisateur): bool
  {
    $utilisateur->tokens()->delete();
    $this->logOperation('revoke_all_tokens', 'utilisateur', $utilisateur->id);
    return true;
  }

  /**
   * Révoquer un token spécifique.
   */
  public function revokeToken(Utilisateur $utilisateur, string $tokenId): bool
  {
    return $utilisateur->tokens()->where('id', $tokenId)->delete() > 0;
  }

  /**
   * Valider un token et retourner l'utilisateur associé.
   */
  public function validateToken(string $token): ?Utilisateur
  {
    [$id, $tokenValue] = explode('|', $token, 2);

    $tokenModel = PersonalAccessToken::find($id);

    if (!$tokenModel || !hash_equals($tokenModel->token, hash('sha256', $tokenValue))) {
      return null;
    }

    if ($tokenModel->expires_at && $tokenModel->expires_at->isPast()) {
      return null;
    }

    return $tokenModel->tokenable;
  }

  /**
   * Nettoyer les tokens expirés.
   */
  public function cleanExpiredTokens(): int
  {
    return PersonalAccessToken::where('expires_at', '<', now())->delete();
  }
}
