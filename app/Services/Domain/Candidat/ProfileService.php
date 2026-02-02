<?php

namespace App\Services\Domain\Candidat;

use App\Models\Candidat;
use App\Traits\HasOptimizedUpdate;
use App\Traits\HasActivityLogger;
use App\Services\Infrastructure\Logger\ActivityLoggerService;

class ProfileService
{
  use HasOptimizedUpdate, HasActivityLogger;

  public function __construct(ActivityLoggerService $logger)
  {
    $this->logger = $logger;
  }

  /**
   * Récupérer le profil complet d'un candidat
   */
  public function getProfile(Candidat $candidat): Candidat
  {
    $candidat->load(['utilisateur', 'filiere', 'candidatures.concours.ecole']);
    $this->logView('candidat_profile', $candidat->utilisateur_id);
    return $candidat;
  }

  /**
   * Mettre à jour le profil d'un candidat (complétion des informations)
   */
  public function updateProfile(Candidat $candidat, array $data): Candidat
  {
    $allowedFields = [
      // Identité
      'nom_cand',
      'prenom_cand',
      'sexe_cand',
      'date_naissance_cand',
      'nationalite_cand',
      'lieu_naissance_cand',
      'ethnie_cand',

      // Contact
      'adresse_cand',
      'region',
      'departement',
      'arrondissement',

      // Documents d'identité
      'numero_cni',
      'date_delivrance_cni',

      // Scolarité (complétion)
      'niveau_scolaire',
      'filiere_origine',
      'etablissement_origine',
      'ville_etablissement',
      'diplome_admission',
      'serie_bac',
      'annee_obtention_bac',
      'mention',
      'annee_diplome',

      // Tuteur/Parent
      'nom_tuteur_cand',
      'telephone_tuteur_cand',
      'nom_parent',
      'telephone_parent',
      'nom_pere',
      'telephone_pere',

      // Autres
      'statut_matrimonial',
      'a_handicap',
      'type_handicap',
      'premiere_langue',
      'autre_langue',
    ];


    $filteredData = array_intersect_key($data, array_flip($allowedFields));

    $readOnlyFields = ['nationalite_cand', 'date_naissance_cand', 'diplome_admission'];
    foreach ($readOnlyFields as $field) {
      if (!empty($candidat->$field) && isset($filteredData[$field])) {
        unset($filteredData[$field]);
      }
    }


    $this->updateIfDirty($candidat, $filteredData);
    $this->logUpdate('candidat_profile', $candidat->utilisateur_id, array_keys($filteredData));

    return $candidat->fresh();
  }
}
