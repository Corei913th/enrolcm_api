<?php

namespace App\Enums;

enum StatutCandidature: string
{
  case BROUILLON = 'BROUILLON';
  case SOUMISE = 'SOUMISE';
  case DOCUMENTS_VERIFIES = 'DOCUMENTS_VERIFIES';
  case PAIEMENT_VERIFIE = 'PAIEMENT_VERIFIE';
  case VALIDE = 'VALIDE';
  case REJETEE = 'REJETEE';
  case ANNULEE = 'ANNULEE';

  /**
   * Vérifie si la candidature peut être modifiée
   */
  public function peutEtreModifiee(): bool
  {
    return in_array($this, [self::BROUILLON, self::SOUMISE, self::REJETEE]);
  }

  /**
   * Vérifie si la candidature est en attente de validation
   */
  public function enAttenteValidation(): bool
  {
    return in_array($this, [self::SOUMISE, self::DOCUMENTS_VERIFIES, self::PAIEMENT_VERIFIE]);
  }

  /**
   * Vérifie si la candidature est complètement validée
   */
  public function estValidee(): bool
  {
    return $this === self::VALIDE;
  }

  /**
   * Vérifie si la candidature est soumise
   */
  public function estSoumise(): bool
  {
    return $this === self::SOUMISE;
  }

  /**
   * Vérifie si la candidature est rejetée
   */
  public function estRejetee(): bool
  {
    return $this === self::REJETEE;
  }

  /**
   * Vérifie si la candidature est active
   */
  public function estActive(): bool
  {
    return in_array($this, [self::SOUMISE, self::DOCUMENTS_VERIFIES, self::PAIEMENT_VERIFIE, self::VALIDE]);
  }

  /**
   * Liste des valeurs pour les migrations
   */
  public static function values(): array
  {
    return array_column(self::cases(), 'value');
  }

  /**
   * Labels pour l'affichage
   */
  public function label(): string
  {
    return match ($this) {
      self::BROUILLON => 'Brouillon',
      self::SOUMISE => 'Soumise',
      self::DOCUMENTS_VERIFIES => 'Documents vérifiés',
      self::PAIEMENT_VERIFIE => 'Paiement vérifié',
      self::VALIDE => 'Validée',
      self::REJETEE => 'Rejetée',
      self::ANNULEE => 'Annulée',
    };
  }

  /**
   * Couleur pour l'affichage (Bootstrap classes)
   */
  public function couleur(): string
  {
    return match ($this) {
      self::BROUILLON => 'secondary',
      self::SOUMISE => 'warning',
      self::DOCUMENTS_VERIFIES => 'info',
      self::PAIEMENT_VERIFIE => 'primary',
      self::VALIDE => 'success',
      self::REJETEE => 'danger',
      self::ANNULEE => 'dark',
    };
  }
}
