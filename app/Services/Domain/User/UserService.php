<?php

namespace App\Services\Domain\User;

use App\DTOs\Users\CreateUserDTO;
use App\Enums\TypeUtilisateur;
use App\Models\Admin;
use App\Models\Correcteur;
use App\Models\ResponsableCentre;
use App\Models\Utilisateur;
use App\Services\Domain\User\RoleService;
use App\Services\Domain\User\TokenService;
use App\Traits\HasOptimizedUpdate;
use App\Traits\HasActivityLogger;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Business\UserException;
use App\Traits\HasAdvancedSearch;

class UserService
{
  use HasOptimizedUpdate, HasActivityLogger, HasAdvancedSearch;

  public function __construct(
    private readonly RoleService $roleService,
    private readonly TokenService $tokenService,
    ActivityLoggerService $logger
  ) {
    $this->logger = $logger;
  }

  /**
   * Créer un utilisateur candidat avec PRU comme username.
   */
  public function createCandidatUser(string $pru, string $email, string $password, string $telephone): Utilisateur
  {
    $user = Utilisateur::create([
      'user_name' => $pru,
      'email' => $email,
      'mot_de_passe' => Hash::make($password),
      'telephone' => $telephone,
      'type_utilisateur' => TypeUtilisateur::CANDIDAT,
      'est_actif' => true,
      'email_verifie' => false,
    ]);

    $this->logCreate('utilisateur', $user->id, ['email' => $email, 'type' => 'CANDIDAT']);
    return $user;
  }

  /**
   * Créer un utilisateur candidat pour le workflow d'inscription 
   */
  public function createCandidatUserForRegistration(string $email, string $password, ?string $telephone = null): Utilisateur
  {
    $user = Utilisateur::create([
      'user_name' => $email,
      'email' => $email,
      'mot_de_passe' => Hash::make($password),
      'telephone' => $telephone,
      'type_utilisateur' => TypeUtilisateur::CANDIDAT,
      'est_actif' => true,
      'email_verifie' => false,
    ]);

    $this->logCreate('utilisateur', $user->id, ['email' => $email, 'type' => 'CANDIDAT']);
    return $user;
  }

  /**
   * Vérifier si un email existe déjà.
   */
  public function emailExists(string $email): bool
  {
    return Utilisateur::where('email', $email)->exists();
  }

  /**
   * Authentifier un candidat avec email + mot de passe.
   */
  public function authenticateCandidat(string $email, string $password): ?Utilisateur
  {
    $utilisateur = Utilisateur::where('email', $email)
      ->where('type_utilisateur', TypeUtilisateur::CANDIDAT)
      ->where('est_actif', true)
      ->first();

    if (!$utilisateur || !Hash::check($password, $utilisateur->mot_de_passe)) {
      return null;
    }

    return $utilisateur;
  }

  /**
   * Générer un token pour un utilisateur
   */
  public function generateToken(Utilisateur $utilisateur): array
  {
    return $this->tokenService->generateToken($utilisateur);
  }

  /**
   * Désactiver un utilisateur.
   */
  public function deactivate(string $utilisateurId): bool
  {
    return runTransaction(function () use ($utilisateurId) {
      $utilisateur = Utilisateur::findOrFail($utilisateurId);
      $utilisateur->update(['est_actif' => false]);
      $this->logStatusChange('utilisateur', $utilisateurId, false);
      return true;
    }, 'UserService::deactivate');
  }

  /**
   * Activer un utilisateur.
   */
  public function activate(string $utilisateurId): bool
  {
    $utilisateur = Utilisateur::findOrFail($utilisateurId);
    $utilisateur->update(['est_actif' => true]);
    $this->logStatusChange('utilisateur', $utilisateurId, true);
    return true;
  }

  /**
   * Créer un utilisateur staff (admin, responsable centre, correcteur).
   */
  public function createStaff(CreateUserDTO $dto): Utilisateur
  {
    return runTransaction(function () use ($dto) {
      $typeUtilisateur = TypeUtilisateur::from($dto->type_utilisateur);

      $user = Utilisateur::create([
        'email' => $dto->email,
        'user_name' => $dto->user_name,
        'mot_de_passe' => Hash::make($dto->mot_de_passe),
        'type_utilisateur' => $typeUtilisateur,
        'email_verifie' => false,
        'telephone' => $dto->telephone,
      ]);

      $this->completeStaffWithRole($dto, $user);
      $this->logCreate('utilisateur', $user->id, ['email' => $dto->email, 'type' => $typeUtilisateur->value]);

      return $user;
    }, 'UserService::createStaff');
  }

  /**
   * Compléter la création d'un staff avec son rôle spécifique.
   */
  public function completeStaffWithRole(CreateUserDTO $dto, Utilisateur $user): void
  {
    $typeUtilisateur = TypeUtilisateur::from($dto->type_utilisateur);

    switch ($typeUtilisateur) {
      case TypeUtilisateur::ADMIN:
        Admin::create([
          'utilisateur_id' => $user->id,
          'matricule' => $dto->matricule
        ]);
        $this->assignRole($user, TypeUtilisateur::ADMIN);
        break;

      case TypeUtilisateur::RESPONSABLE_CENTRE:
        ResponsableCentre::create([
          'utilisateur_id' => $user->id,
          'code_agent' => $dto->code_agent
        ]);
        $this->assignRole($user, TypeUtilisateur::RESPONSABLE_CENTRE);
        break;

      case TypeUtilisateur::CORRECTEUR:
        Correcteur::create([
          'utilisateur_id' => $user->id,
          'matricule_enseignant' => $dto->matricule_enseignant,
          'specialite' => $dto->specialite,
        ]);
        $this->assignRole($user, TypeUtilisateur::CORRECTEUR);
        break;
    }
  }

  /**
   * Assigner un rôle à un utilisateur.
   */
  private function assignRole(Utilisateur $user, TypeUtilisateur $roleName): void
  {
    $this->roleService->assignRole($user, $roleName);
  }

  /**
   * Récupérer tous les utilisateurs avec filtres.
   */
  public function getAll(array $filters = [], int $perPage = 15)
  {
    $query = Utilisateur::query()->with(['admin', 'correcteur', 'responsableCentre']);


    $query->where('type_utilisateur', '!=', TypeUtilisateur::CANDIDAT);


    if (isset($filters['search'])) {
      $this->applySearch(
        $query,
        $filters['search'],
        [
          'user_name' => 'partial',
          'email' => 'partial',
          'telephone' => 'partial'
        ]
      );
    }

    // Appliquer les filtres
    $filterData = [];
    if (isset($filters['type_utilisateur'])) {
      $filterData['type_utilisateur'] = $filters['type_utilisateur'];
    }
    if (isset($filters['est_actif'])) {
      $filterData['est_actif'] = filter_var($filters['est_actif'], FILTER_VALIDATE_BOOLEAN);
    }
    $this->applyFilters($query, $filterData);

    // Appliquer le tri 
    $this->applySort(
      $query,
      $filters['sort_by'] ?? null,
      $filters['sort_order'] ?? 'desc',
      'created_at',
      ['user_name', 'email', 'created_at', 'type_utilisateur', 'est_actif']
    );

    return $query->paginate($perPage);
  }

  /**
   * Récupérer un utilisateur par ID.
   */
  public function getById(string $id): Utilisateur
  {
    try {
      return Utilisateur::findOrFail($id);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      throw UserException::notFound($id);
    }
  }

  /**
   * Mettre à jour un utilisateur.
   */
  public function update(string $id, array $data): Utilisateur
  {
    return runTransaction(function () use ($id, $data) {
      try {
        $user = $this->getById($id);

        $updateData = [
          'user_name' => $data['user_name'] ?? $user->user_name,
          'email' => $data['email'] ?? $user->email,
          'telephone' => $data['telephone'] ?? $user->telephone,
          'est_actif' => isset($data['est_actif']) ? $data['est_actif'] : $user->est_actif,
        ];

        $this->updateIfDirty($user, $updateData);
        $this->logUpdate('utilisateur', $id, $updateData);

        return $user;
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        throw UserException::notFound($id);
      } catch (\Exception $e) {
        throw new UserException("Erreur lors de la mise à jour : " . $e->getMessage(), 500);
      }
    }, 'UserService::update');
  }

  /**
   * Supprimer un utilisateur.
   */
  public function delete(string $id): void
  {
    try {
      $user = Utilisateur::findOrFail($id);
      $user->delete();
      $this->logDelete('utilisateur', $id);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      throw UserException::notFound($id);
    } catch (\Exception $e) {
      throw new UserException("Erreur lors de la suppression : " . $e->getMessage(), 500);
    }
  }
}
