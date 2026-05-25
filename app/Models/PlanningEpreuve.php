<?php

namespace App\Models;

use App\Services\Domain\Concours\Validators\PlanningEpreuveValidator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPlanningEpreuve
 */
class PlanningEpreuve extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'planning_epreuves';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'epreuve_id',
        'concours_id',
        'session_id',
        'coefficient',
        'centre_id',
        'date_epreuve',
        'heure_debut',
        'heure_fin',
        'instructions',
        'est_actif',
    ];

    protected $casts = [
        'coefficient' => 'integer',
        'date_epreuve' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function epreuve()
    {
        return $this->belongsTo(Epreuve::class, 'epreuve_id', 'id_epreuve');
    }

    public function concours()
    {
        return $this->belongsTo(Concours::class, 'concours_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class, 'centre_id');
    }

    public function affectationsSalles()
    {
        return $this->hasMany(CandidatureSalle::class, 'planning_epreuve_id');
    }

    // Relation transitive vers centres via salles
    public function getCentres()
    {
        return $this->affectationsSalles()
            ->with('salle.centre')
            ->get()
            ->pluck('salle.centre')
            ->unique('id');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeParDate($query, $date)
    {
        return $query->whereDate('date_epreuve', $date);
    }

    public function scopeAVenir($query)
    {
        return $query->where('date_epreuve', '>=', now()->toDateString());
    }

    // Helpers
    public function getDureeEnMinutes()
    {
        $debut = Carbon::parse($this->heure_debut);
        $fin = Carbon::parse($this->heure_fin);

        return $debut->diffInMinutes($fin);
    }

    public function getHeureDebutFormatee()
    {
        return Carbon::parse($this->heure_debut)->format('H:i');
    }

    public function getHeureFinFormatee()
    {
        return Carbon::parse($this->heure_fin)->format('H:i');
    }

    /**
     * Get effective coefficient (specific to concours or default)
     */
    public function getCoefficientEffectif(): int
    {
        return $this->coefficient ?? $this->epreuve->coefficient_defaut ?? 1;
    }

    /**
     * Boot method to add automatic validations
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($planning) {
            $validator = app(PlanningEpreuveValidator::class);
            $validator->validateBeforeSave($planning);
        });
    }
}
