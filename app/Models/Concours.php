<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Schema;

class Concours extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'concours';

    /**
     * Cache pour vérifier si session_id existe dans concours_filiere
     */
    private static ?bool $hasSessionIdColumn = null;

    protected $fillable = [
        'spec_concours_id',
        'libelle_concours',
        'description',
        'date_limite_depot',
        'date_examen',
        'nbre_max_places',
        'frais_inscription',
        'est_actif',
    ];

    protected $casts = [
        'date_limite_depot' => 'date',
        'date_examen' => 'date',
        'nbre_max_places' => 'integer',
        'frais_inscription' => 'decimal:2',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'concours_session')
            ->using(ConcoursSession::class)
            ->withTimestamps();
    }

    public function specConcours()
    {
        return $this->belongsTo(SpecConcours::class, 'spec_concours_id');
    }

    /**
     * Vérifier si la colonne session_id existe dans concours_filiere (avec cache)
     */
    private static function hasSessionIdColumn(): bool
    {
        if (self::$hasSessionIdColumn === null) {
            self::$hasSessionIdColumn = Schema::hasColumn('concours_filiere', 'session_id');
        }
        return self::$hasSessionIdColumn;
    }

    public function filieres()
    {
        $pivotColumns = ['nombre_places'];

        // Ajouter session_id seulement si la colonne existe dans la table pivot
        if (self::hasSessionIdColumn()) {
            $pivotColumns[] = 'session_id';
        }

        return $this->belongsToMany(Filiere::class, 'concours_filiere')
            ->using(ConcoursFiliere::class)
            ->withPivot($pivotColumns)
            ->withTimestamps();
    }

    // RELATION AVANCÉE : Filières par session
    public function filieresParSession($sessionId)
    {
        $pivotColumns = ['nombre_places'];

        // Ajouter session_id seulement si la colonne existe dans la table pivot
        if (self::hasSessionIdColumn()) {
            $pivotColumns[] = 'session_id';

            return $this->belongsToMany(Filiere::class, 'concours_filiere')
                ->wherePivot('session_id', $sessionId)
                ->withPivot($pivotColumns)
                ->withTimestamps();
        }

        // Si session_id n'existe pas, retourner toutes les filières sans filtre
        return $this->belongsToMany(Filiere::class, 'concours_filiere')
            ->withPivot($pivotColumns)
            ->withTimestamps();
    }

    public function concoursSessions()
    {
        return $this->hasMany(ConcoursSession::class, 'concours_id');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'concours_id');
    }

    public function etatsConcours()
    {
        return $this->hasMany(EtatConcoursSession::class, 'concours_session_concours_id');
    }

    public function configurationPaiement()
    {
        return $this->hasOne(ConcoursPaiement::class, 'concours_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'concours_id');
    }

    public function isOuvert(): bool
    {
        return $this->est_actif && now()->lte($this->date_limite_depot);
    }

    // GESTION DES PLACES PAR SESSION
    public function getPlacesDisponiblesPourSession($sessionId, $filiereId = null): int
    {
        $query = $this->filieresParSession($sessionId);

        if ($filiereId) {
            $query->where('filieres.id', $filiereId);
        }

        return $query->sum('pivot.nombre_places');
    }

    public function getPlacesOccupeesPourSession($sessionId, $filiereId = null): int
    {
        $query = $this->candidatures()
            ->where('session_id', $sessionId)
            ->where('statut_candidature', 'VALIDE'); // Uniquement les candidatures validées

        if ($filiereId) {
            $query->whereHas('candidat', function ($q) use ($filiereId) {
                $q->where('filiere_id', $filiereId);
            });
        }

        return $query->count();
    }

    public function placesRestantesPourSession($sessionId, $filiereId = null): int
    {
        $disponibles = $this->getPlacesDisponiblesPourSession($sessionId, $filiereId);
        $occupees = $this->getPlacesOccupeesPourSession($sessionId, $filiereId);

        return max(0, $disponibles - $occupees);
    }

    public function peutAccepterCandidaturePourSession($sessionId, $filiereId = null): bool
    {
        return $this->isOuvert() &&
            $this->placesRestantesPourSession($sessionId, $filiereId) > 0;
    }
}
