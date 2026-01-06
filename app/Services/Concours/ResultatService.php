<?php

namespace App\Services\Concours;

use App\Models\ResultatFinal;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Session;
use App\Models\Note;
use App\Enums\StatutCandidature;
use App\Enums\DecisionAdmission;
use App\Enums\Mention;
use App\Exceptions\ConcoursException;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;


class ResultatService
{
  public function __construct(
    private readonly NoteService $noteService
  ) {}

  /**
   * Calculer tous les résultats pour un concours et une session.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   *
   * @return array Résumé du calcul
   */
  public function calculerResultats(string $concoursId, string $sessionId): array
  {
    $candidatures = $this->getCandidaturesValidees($concoursId, $sessionId);
    $stats = $this->initialiserStatsCalcul($candidatures);

    DB::transaction(function () use ($candidatures, &$stats) {
      foreach ($candidatures as $candidature) {
        $this->traiterCandidaturePourCalcul($candidature, $stats);
      }
    });

    $this->calculerRangsParFiliere($concoursId, $sessionId);

    return $stats;
  }

  /**
   * Récupère les candidatures validées pour un concours et une session.
   */
  private function getCandidaturesValidees(string $concoursId, string $sessionId)
  {
    return Candidature::where('concours_id', $concoursId)
      ->where('session_id', $sessionId)
      ->where('statut_candidature', StatutCandidature::VALIDE)
      ->with(['candidat.filiere'])
      ->get();
  }

  /**
   * Initialise les statistiques de calcul.
   */
  private function initialiserStatsCalcul(Collection $candidatures): array
  {
    return [
      'total_candidatures' => $candidatures->count(),
      'resultats_calcules' => 0,
      'erreurs' => [],
    ];
  }

  /**
   * Traite une candidature pour le calcul des résultats.
   */
  private function traiterCandidaturePourCalcul(Candidature $candidature, array &$stats): void
  {
    try {
      $calcul = $this->noteService->calculerMoyenneGenerale($candidature->id);

      if ($calcul['notes_validees'] > 0) {
        $this->creerOuMettreAJourResultat($candidature, $calcul);
        $stats['resultats_calcules']++;
      }
    } catch (\Exception $e) {
      $stats['erreurs'][] = [
        'candidature_id' => $candidature->id,
        'erreur' => $e->getMessage(),
      ];
    }
  }

  /**
   * Crée ou met à jour le résultat final d'une candidature.
   */
  private function creerOuMettreAJourResultat(Candidature $candidature, array $calcul): ResultatFinal
  {
    return ResultatFinal::updateOrCreate(
      ['candidature_id' => $candidature->id],
      [
        'moyenne_generale' => $calcul['moyenne'],
        'total_point' => $calcul['total_points'],
        'rang' => null, // Sera calculé après
        'decision' => null, // Sera déterminé selon le classement
        'mention' => $this->determinerMention($calcul['moyenne']),
        'est_admis' => false, // Sera déterminé selon le classement
      ]
    );
  }

  /**
   * Calculer les rangs par filière.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   */
  private function calculerRangsParFiliere(string $concoursId, string $sessionId): void
  {
    $filieres = $this->getFilieresAvecPlaces($concoursId, $sessionId);

    foreach ($filieres as $filiere) {
      $this->assignerRangsPourFiliere($concoursId, $sessionId, $filiere);
    }
  }

  /**
   * Assigne les rangs pour une filière spécifique.
   */
  private function assignerRangsPourFiliere(string $concoursId, string $sessionId, $filiere): void
  {
    $resultats = $this->getResultatsTriesPourFiliere($concoursId, $sessionId, $filiere);

    $rang = 1;
    foreach ($resultats as $resultat) {
      $resultat->update(['rang' => $rang]);
      $rang++;
    }
  }

  /**
   * Récupère les résultats triés pour une filière.
   */
  private function getResultatsTriesPourFiliere(string $concoursId, string $sessionId, $filiere)
  {
    return ResultatFinal::whereHas('candidature', function ($query) use ($concoursId, $sessionId, $filiere) {
      $query->where('concours_id', $concoursId)
        ->where('session_id', $sessionId)
        ->whereHas('candidat', function ($subQuery) use ($filiere) {
          $subQuery->where('filiere_id', $filiere->id);
        });
    })
      ->orderBy('moyenne_generale', 'desc')
      ->orderBy('total_point', 'desc')
      ->get();
  }

  /**
   * Déterminer les admissions selon les places disponibles.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   *
   * @return array Statistiques des admissions
   */
  public function determinerAdmissions(string $concoursId, string $sessionId): array
  {
    $stats = $this->initialiserStatsAdmissions();

    DB::transaction(function () use ($concoursId, $sessionId, &$stats) {
      $filieres = $this->getFilieresAvecPlaces($concoursId, $sessionId);

      foreach ($filieres as $filiere) {
        $this->traiterAdmissionsParFiliere($concoursId, $sessionId, $filiere, $stats);
      }
    });

    return $stats;
  }

  /**
   * Initialise les statistiques d'admission.
   */
  private function initialiserStatsAdmissions(): array
  {
    return [
      'total_candidatures' => 0,
      'admis' => 0,
      'liste_attente' => 0,
      'non_admis' => 0,
    ];
  }

  /**
   * Récupère les filières avec leurs places disponibles.
   */
  private function getFilieresAvecPlaces(string $concoursId, string $sessionId)
  {
    return Concours::find($concoursId)->filieresParSession($sessionId);
  }

  /**
   * Traite les admissions pour une filière spécifique.
   */
  private function traiterAdmissionsParFiliere(string $concoursId, string $sessionId, $filiere, array &$stats): void
  {
    $placesDisponibles = $filiere->pivot->nombre_places;
    $resultats = $this->getResultatsTriesParFiliere($concoursId, $sessionId, $filiere);

    $stats['total_candidatures'] += $resultats->count();

    foreach ($resultats as $index => $resultat) {
      $decision = $this->determinerDecisionAdmission($index, $placesDisponibles);
      $this->appliquerDecisionAdmission($resultat, $decision, $stats);
    }
  }

  /**
   * Récupère les résultats triés par rang pour une filière.
   */
  private function getResultatsTriesParFiliere(string $concoursId, string $sessionId, $filiere)
  {
    return ResultatFinal::whereHas('candidature', function ($query) use ($concoursId, $sessionId, $filiere) {
      $query->where('concours_id', $concoursId)
        ->where('session_id', $sessionId)
        ->whereHas('candidat', function ($subQuery) use ($filiere) {
          $subQuery->where('filiere_id', $filiere->id);
        });
    })->orderBy('rang')->get();
  }

  /**
   * Détermine la décision d'admission selon la position et les places disponibles.
   */
  private function determinerDecisionAdmission(int $position, int $placesDisponibles): DecisionAdmission
  {
    if ($position < $placesDisponibles) {
      return DecisionAdmission::ADMIS;
    }

    if ($position < $placesDisponibles * 1.5) { // Liste d'attente (50% supplémentaires)
      return DecisionAdmission::LISTE_ATTENTE;
    }

    return DecisionAdmission::REFUSEE;
  }

  /**
   * Applique la décision d'admission et met à jour les statistiques.
   */
  private function appliquerDecisionAdmission(ResultatFinal $resultat, DecisionAdmission $decision, array &$stats): void
  {
    $estAdmis = $decision === DecisionAdmission::ADMIS;

    $resultat->update([
      'est_admis' => $estAdmis,
      'decision' => $decision,
    ]);

    $this->incrementerStatistiquesAdmission($decision, $stats);
  }

  /**
   * Incrémente les statistiques selon la décision.
   */
  private function incrementerStatistiquesAdmission(DecisionAdmission $decision, array &$stats): void
  {
    match ($decision) {
      DecisionAdmission::ADMIS => $stats['admis']++,
      DecisionAdmission::LISTE_ATTENTE => $stats['liste_attente']++,
      DecisionAdmission::REFUSEE => $stats['non_admis']++,
    };
  }

  /**
   * Publier les résultats (rendre visible aux candidats).
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   *
   * @return int Nombre de résultats publiés
   */
  public function publierResultats(string $concoursId, string $sessionId): int
  {
    return ResultatFinal::whereHas('candidature', function ($query) use ($concoursId, $sessionId) {
      $query->where('concours_id', $concoursId)
        ->where('session_id', $sessionId);
    })
      ->whereNull('date_publication')
      ->update(['date_publication' => now()]);
  }

  /**
   * Déterminer la mention selon la moyenne.
   *
   * @param float $moyenne Moyenne générale
   *
   * @return Mention|null Mention obtenue
   */
  private function determinerMention(float $moyenne): ?Mention
  {
    if ($moyenne >= 16) {
      return Mention::TRES_BIEN;
    } elseif ($moyenne >= 14) {
      return Mention::BIEN;
    } elseif ($moyenne >= 12) {
      return Mention::ASSEZ_BIEN;
    } elseif ($moyenne >= 10) {
      return Mention::PASSABLE;
    }

    return null;
  }

  /**
   * Obtenir les résultats d'un candidat.
   *
   * @param string $candidatureId ID de la candidature
   *
   * @return ResultatFinal|null Résultat du candidat
   */
  public function getResultatCandidat(string $candidatureId): ?ResultatFinal
  {
    return ResultatFinal::where('candidature_id', $candidatureId)
      ->with(['candidature.candidat', 'candidature.concours'])
      ->first();
  }

  /**
   * Obtenir le classement d'une filière.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $filiereId ID de la filière
   *
   * @return Collection Classement de la filière
   */
  public function getClassementFiliere(string $concoursId, string $sessionId, string $filiereId): Collection
  {
    return ResultatFinal::whereHas('candidature', function ($query) use ($concoursId, $sessionId, $filiereId) {
      $query->where('concours_id', $concoursId)
        ->where('session_id', $sessionId)
        ->whereHas('candidat', function ($subQuery) use ($filiereId) {
          $subQuery->where('filiere_id', $filiereId);
        });
    })
      ->with(['candidature.candidat'])
      ->orderBy('rang')
      ->get();
  }
}
