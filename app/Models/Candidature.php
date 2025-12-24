<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidature extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'candidatures';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'candidat_id',
        'concours_id',
        'session_id',
        'date_candidature',
        'code_cand_temp',
        'code_cand_def',
        'qr_code',
        'date_inscription',
        'date_depot_physique',
        'date_validation',
        'motif_rejet',
    ];

    protected $casts = [
        'date_candidature' => 'datetime',
        'date_inscription' => 'date',
        'date_depot_physique' => 'date',
        'date_validation' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations
    public function candidat()
    {
        return $this->belongsTo(Candidat::class, 'candidat_id', 'utilisateur_id');
    }

    public function concours()
    {
        return $this->belongsTo(Concours::class, 'concours_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function concoursSession()
    {
        return $this->belongsTo(ConcoursSession::class, ['concours_id', 'session_id'], ['concours_id', 'session_id']);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'candidature_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'candidature_id');
    }

    public function resultatFinal()
    {
        return $this->hasOne(ResultatFinal::class, 'candidature_id');
    }

    public function etatsCandidature()
    {
        return $this->hasMany(EtatCandidature::class, 'candidature_id');
    }

    public function etatActuel()
    {
        return $this->hasOne(EtatCandidature::class, 'candidature_id')
            ->latest('date_etat');
    }

    // Scopes
    public function scopeValidees($query)
    {
        return $query->whereNotNull('date_validation');
    }

    public function scopeEnAttente($query)
    {
        return $query->whereNull('date_validation')
            ->whereNull('motif_rejet');
    }

    public function scopeRejetees($query)
    {
        return $query->whereNotNull('motif_rejet');
    }

    public function scopeByConcours($query, $concoursId)
    {
        return $query->where('concours_id', $concoursId);
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeByConcoursSession($query, $concoursId, $sessionId)
    {
        return $query->where('concours_id', $concoursId)
            ->where('session_id', $sessionId);
    }

    // Helpers
    public function isValidee()
    {
        return !is_null($this->date_validation);
    }

    public function isRejetee()
    {
        return !is_null($this->motif_rejet);
    }

    public function hasCodeDefinitif()
    {
        return !is_null($this->code_cand_def);
    }

    public function genererCodeTemporaire()
    {
        if (!$this->code_cand_temp) {
            $this->code_cand_temp = 'TEMP-' . strtoupper(uniqid());
            $this->save();
        }
        return $this->code_cand_temp;
    }

    public function genererCodeDefinitif()
    {
        if (!$this->code_cand_def && $this->isValidee()) {
            $annee = date('Y');
            $numero = str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->code_cand_def = "CAND-{$annee}-{$numero}";
            $this->save();
        }
        return $this->code_cand_def;
    }

    public function getMoyenneGenerale()
    {
        return $this->resultatFinal ? $this->resultatFinal->moyenne_generale : null;
    }

    public function isAdmis()
    {
        return $this->resultatFinal && $this->resultatFinal->est_admis;
    }

    public function getLibelleConcoursSession()
    {
        if ($this->concours && $this->session) {
            return "{$this->concours->libelle_concours} - {$this->session->libelle_session}";
        }
        return null;
    }

    public function getDateLimiteDepot()
    {
        return $this->concours ? $this->concours->date_limite_depot : null;
    }

    public function getDateExamen()
    {
        return $this->concours ? $this->concours->date_examen : null;
    }

    public function canDeposerDossier()
    {
        return $this->concours 
            && $this->concours->isOuvert() 
            && $this->session 
            && $this->session->est_actif
            && !$this->isValidee()
            && !$this->isRejetee();
    }
}
