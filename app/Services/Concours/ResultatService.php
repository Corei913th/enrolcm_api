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
    // Récupérer toutes les candidatures validées pour ce concours/session
    $candidatures = Candidature::where('concours_id', $concoursId)
      ->where('session_id', $sessionId)
      ->where('statut_candidature', StatutCandidature::VALIDE)
      ->with(['candidat.filiere'])
      ->get();

    $resultats = [];
    $stats = [
      'total_candidatures' => $candidatures->count(),
      'resultats_calcules' => 0,
      'erreurs' => [],
    ];

    DB::transaction(function () use ($candidatures, &$resultats, &$stats) {
      foreach ($candidatures as $candidature) {
        try {
          // Calculer la moyenne
          $calcul = $this->noteService->calculerMoyenneGenerale($candidature->id);

          if ($calcul['notes_validees'] > 0) {
            // Créer ou mettre à jour le résultat final
            $resultat = ResultatFinal::updateOrCreate(
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

            $resultats[] = $resultat;
            $stats['resultats_calcules']++;
          }
        } catch (\Exception $e) {
          $stats['erreurs'][] = [
            'candidature_id' => $candidature->id,
            'erreur' => $e->getMessage(),
          ];
        }
      }
    });

    // Calculer les rangs par filière
    $this->calculerRangsParFiliere($concoursId, $sessionId);

    return $stats;
  }

  /**
   * Calculer les rangs par filière.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   */
  private function calculerRangsParFiliere(string $concoursId, string $sessionId): void
  {
    // Récupérer les filières associées au concours
    $filieres = Concours::find($concoursId)->filieresParSession($sessionId);

    foreach ($filieres as $filiere) {
      // Récupérer les résultats pour cette filière
      $resultats = ResultatFinal::whereHas('candidature', function ($query) use ($concoursId, $sessionId, $filiere) {
        $query->where('concours_id', $concoursId)
          ->where('session_id', $sessionId)
          ->whereHas('candidat', function ($subQuery) use ($filiere) {
            $subQuery->where('filiere_id', $filiere->id);
          });
      })
        ->orderBy('moyenne_generale', 'desc')
        ->orderBy('total_point', 'desc')
        ->get();

      // Assigner les rangs
      $rang = 1;
      foreach ($resultats as $resultat) {
        $resultat->update(['rang' => $rang]);
        $rang++;
      }
    }
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
    $stats = [
      'total_candidatures' => 0,
      'admis' => 0,
      'liste_attente' => 0,
      'non_admis' => 0,
    ];

    DB::transaction(function () use ($concoursId, $sessionId, &$stats) {
      // Récupérer les filières avec leurs places
      $filieres = Concours::find($concoursId)->filieresParSession($sessionId);

      foreach ($filieres as $filiere) {
        $placesDisponibles = $filiere->pivot->nombre_places;

        // Récupérer les résultats triés par rang pour cette filière
        $resultats = ResultatFinal::whereHas('candidature', function ($query) use ($concoursId, $sessionId, $filiere) {
          $query->where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->whereHas('candidat', function ($subQuery) use ($filiere) {
              $subQuery->where('filiere_id', $filiere->id);
            });
        })
          ->orderBy('rang')
          ->get();

        $stats['total_candidatures'] += $resultats->count();

        foreach ($resultats as $index => $resultat) {
          if ($index < $placesDisponibles) {
            // Admis
            $resultat->update([
              'est_admis' => true,
              'decision' => DecisionAdmission::ADMIS,
            ]);
            $stats['admis']++;
          } elseif ($index < $placesDisponibles * 1.5) { // Liste d'attente (50% supplémentaires)
            // Liste d'attente
            $resultat->update([
              'est_admis' => false,
              'decision' => DecisionAdmission::LISTE_ATTENTE,
            ]);
            $stats['liste_attente']++;
          } else {
            // Non admis
            $resultat->update([
              'est_admis' => false,
              'decision' => DecisionAdmission::REFUSEE,
            ]);
            $stats['non_admis']++;
          }
        }
      }
    });

    return $stats;
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
