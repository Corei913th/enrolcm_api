<?php

namespace App\Services\Domain\Examen;

use App\Models\PlanningEpreuve;
use App\Models\Concours;
use App\Models\Epreuve;
use App\Exceptions\PlanningException;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class PlanningService
{
  public function __construct(
    private readonly ActivityLoggerService $logger
  ) {}

  /**
   * Schedule an exam for a concours session
   */
  public function scheduleExam(array $data): PlanningEpreuve
  {
    return DB::transaction(function () use ($data) {
      try {
        $concours = Concours::findOrFail($data['concours_id']);

        $this->validateConcoursSession($concours, $data['session_id']);
        $this->validateExamExists($data['epreuve_id']);
        $this->validateExamDate($data['date_epreuve'], $concours->date_examen);
        $this->validateTimeRange($data['heure_debut'], $data['heure_fin']);
        $this->validateNoDuplicate($data['epreuve_id'], $data['concours_id'], $data['session_id']);
        $this->validateNoTimeConflict($data);

        $planning = PlanningEpreuve::create($data);

        $this->logger->logActivity(
          'schedule_exam',
          'planning_epreuve',
          $planning->id,
          [
            'concours_id' => $data['concours_id'],
            'session_id' => $data['session_id'],
            'epreuve_id' => $data['epreuve_id'],
            'date_epreuve' => $data['date_epreuve']
          ]
        );

        return $planning;
      } catch (ModelNotFoundException $e) {
        $this->logger->logError($e, 'schedule_exam_failed');
        throw new PlanningException("Resource not found: " . $e->getMessage(), 404);
      } catch (PlanningException $e) {
        $this->logger->logError($e, 'schedule_exam_validation_failed');
        throw $e;
      } catch (\Exception $e) {
        $this->logger->logError($e, 'schedule_exam_error');
        throw new PlanningException("Error scheduling exam: " . $e->getMessage(), 500);
      }
    });
  }

  /**
   * Update an existing schedule
   */
  public function updateSchedule(string $planningId, array $data): PlanningEpreuve
  {
    return DB::transaction(function () use ($planningId, $data) {
      try {
        $planning = PlanningEpreuve::findOrFail($planningId);

        if (isset($data['date_epreuve'])) {
          $this->validateExamDate($data['date_epreuve']);
        }

        $startTime = $data['heure_debut'] ?? $planning->heure_debut;
        $endTime = $data['heure_fin'] ?? $planning->heure_fin;
        $this->validateTimeRange($startTime, $endTime);

        $planning->update($data);

        $this->logger->logActivity(
          'update_schedule',
          'planning_epreuve',
          $planningId,
          ['updated_fields' => array_keys($data)]
        );

        return $planning->fresh();
      } catch (ModelNotFoundException $e) {
        $this->logger->logError($e, 'update_schedule_not_found');
        throw PlanningException::planningIntrouvable($planningId);
      } catch (PlanningException $e) {
        $this->logger->logError($e, 'update_schedule_validation_failed');
        throw $e;
      } catch (\Exception $e) {
        $this->logger->logError($e, 'update_schedule_error');
        throw new PlanningException("Error updating schedule: " . $e->getMessage(), 500);
      }
    });
  }

  /**
   * Delete a schedule
   */
  public function deleteSchedule(string $planningId): bool
  {
    return DB::transaction(function () use ($planningId) {
      try {
        $planning = PlanningEpreuve::findOrFail($planningId);
        $deleted = $planning->delete();

        $this->logger->logActivity(
          'delete_schedule',
          'planning_epreuve',
          $planningId,
          [
            'concours_id' => $planning->concours_id,
            'session_id' => $planning->session_id,
            'epreuve_id' => $planning->epreuve_id
          ]
        );

        return $deleted;
      } catch (ModelNotFoundException $e) {
        $this->logger->logError($e, 'delete_schedule_not_found');
        throw PlanningException::planningIntrouvable($planningId);
      } catch (\Exception $e) {
        $this->logger->logError($e, 'delete_schedule_error');
        throw new PlanningException("Error deleting schedule: " . $e->getMessage(), 500);
      }
    });
  }

  /**
   * Get all schedules for a concours session
   */
  public function getSchedulesByConcoursSession(string $concoursId, string $sessionId): Collection
  {
    return PlanningEpreuve::where('concours_id', $concoursId)
      ->where('session_id', $sessionId)
      ->with(['epreuve'])
      ->orderBy('date_epreuve')
      ->orderBy('heure_debut')
      ->get();
  }

  /**
   * Get a single schedule by ID
   */
  public function getScheduleById(string $planningId): PlanningEpreuve
  {
    try {
      return PlanningEpreuve::with(['epreuve', 'concours', 'session'])->findOrFail($planningId);
    } catch (ModelNotFoundException $e) {
      throw PlanningException::planningIntrouvable($planningId);
    }
  }



  /**
   * Validate that session belongs to concours
   */
  private function validateConcoursSession(Concours $concours, string $sessionId): void
  {
    $sessionExists = $concours->sessions()->where('session_id', $sessionId)->exists();
    if (!$sessionExists) {
      throw PlanningException::concoursSessionInvalide($concours->id, $sessionId);
    }
  }

  /**
   * Validate that exam exists
   */
  private function validateExamExists(string $epreuveId): void
  {
    Epreuve::findOrFail($epreuveId);
  }

  /**
   * Validate that exam date is in the future or today
   * No longer validates against concours.date_examen (deprecated field)
   * Planning dates are the single source of truth
   */
  private function validateExamDate(string $examDate): void
  {
    $examDateParsed = Carbon::parse($examDate);
    $today = Carbon::today();

    if ($examDateParsed->lt($today)) {
      throw PlanningException::dateInvalide(
        $examDateParsed->format('Y-m-d'),
        'La date d\'épreuve ne peut pas être dans le passé'
      );
    }
  }

  /**
   * Validate that end time is after start time
   */
  private function validateTimeRange(string $startTime, string $endTime): void
  {
    $start = Carbon::parse($startTime);
    $end = Carbon::parse($endTime);

    if ($end->lte($start)) {
      throw PlanningException::heuresInvalides($start->format('H:i'), $end->format('H:i'));
    }
  }

  /**
   * Validate no duplicate planning for same exam/concours/session
   */
  private function validateNoDuplicate(string $epreuveId, string $concoursId, string $sessionId): void
  {
    $exists = PlanningEpreuve::where('epreuve_id', $epreuveId)
      ->where('concours_id', $concoursId)
      ->where('session_id', $sessionId)
      ->exists();

    if ($exists) {
      throw PlanningException::epreuveDejaPlannifiee($epreuveId, $concoursId, $sessionId);
    }
  }

  /**
   * Validate no time conflicts with other exams
   */
  private function validateNoTimeConflict(array $data): void
  {
    $startTime = Carbon::parse($data['heure_debut']);
    $endTime = Carbon::parse($data['heure_fin']);

    $hasConflict = PlanningEpreuve::where('concours_id', $data['concours_id'])
      ->where('session_id', $data['session_id'])
      ->where('date_epreuve', $data['date_epreuve'])
      ->where(function ($query) use ($startTime, $endTime) {
        $query->whereBetween('heure_debut', [$startTime, $endTime])
          ->orWhereBetween('heure_fin', [$startTime, $endTime])
          ->orWhere(function ($q) use ($startTime, $endTime) {
            $q->where('heure_debut', '<=', $startTime)
              ->where('heure_fin', '>=', $endTime);
          });
      })
      ->exists();

    if ($hasConflict) {
      throw PlanningException::conflitHoraire(
        Carbon::parse($data['date_epreuve'])->format('Y-m-d'),
        $startTime->format('H:i'),
        $endTime->format('H:i')
      );
    }
  }
}
