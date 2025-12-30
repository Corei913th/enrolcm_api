<?php

namespace App\Models;

use App\Enums\StatutCandidature;
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
        'centre_id',
        'date_candidature',
        'code_cand_temp',
        'code_cand_def',
        'statut_candidature',
        'documents_complets',
        'paiement_valide',
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
        'statut_candidature' => StatutCandidature::class,
        'documents_complets' => 'boolean',
        'paiement_valide' => 'boolean',
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

    public function centre()
    {
        return $this->belongsTo(Centre::class, 'centre_id');
    }

    public function concoursSession()
    {
        return $this->belongsTo(ConcoursSession::class, ['concours_id', 'session_id'], ['concours_id', 'session_id']);
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class, 'candidature_id');
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

    public function affectationsSalles()
    {
        return $this->hasMany(CandidatureSalle::class, 'candidature_id');
    }

    public function convocation()
    {
        return $this->hasOne(Convocation::class, 'candidature_id');
    }

    // Scopes
    public function scopeValidees($query)
    {
        return $query->where('statut_candidature', StatutCandidature::VALIDE->value);
    }

    public function scopeEnAttente($query)
    {
        return $query->whereIn('statut_candidature', [
            StatutCandidature::SOUMISE->value,
            StatutCandidature::DOCUMENTS_VERIFIES->value,
            StatutCandidature::PAIEMENT_VERIFIE->value
        ]);
    }

    public function scopeRejetees($query)
    {
        return $query->where('statut_candidature', StatutCandidature::REJETEE->value);
    }

    public function scopeActives($query)
    {
        return $query->whereIn('statut_candidature', [
            StatutCandidature::SOUMISE->value,
            StatutCandidature::DOCUMENTS_VERIFIES->value,
            StatutCandidature::PAIEMENT_VERIFIE->value,
            StatutCandidature::VALIDE->value
        ]);
    }

    public function scopeBrouillons($query)
    {
        return $query->where('statut_candidature', StatutCandidature::BROUILLON->value);
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
        return $this->statut_candidature === StatutCandidature::VALIDE;
    }

    public function isRejetee()
    {
        return $this->statut_candidature === StatutCandidature::REJETEE;
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

    // Helpers pour statut candidature
    public function peutEtreModifiee(): bool
    {
        return $this->statut_candidature?->peutEtreModifiee() ?? false;
    }

    public function enAttenteValidation(): bool
    {
        return $this->statut_candidature?->enAttenteValidation() ?? false;
    }

    public function estValidee(): bool
    {
        return $this->statut_candidature?->estValidee() ?? false;
    }

    public function estRejetee(): bool
    {
        return $this->statut_candidature?->estRejetee() ?? false;
    }

    public function estActive(): bool
    {
        return $this->statut_candidature?->estActive() ?? false;
    }

    public function soumettre(): void
    {
        $this->update(['statut_candidature' => StatutCandidature::SOUMISE]);
    }

    public function validerDocuments(): void
    {
        $this->update([
            'statut_candidature' => StatutCandidature::DOCUMENTS_VERIFIES,
            'documents_complets' => true
        ]);
    }

    public function validerPaiement(): void
    {
        $this->update([
            'statut_candidature' => StatutCandidature::PAIEMENT_VERIFIE,
            'paiement_valide' => true
        ]);
    }

    public function validerComplete(): void
    {
        $this->update(['statut_candidature' => StatutCandidature::VALIDE]);
    }

    public function rejeter(string $motif): void
    {
        $this->update([
            'statut_candidature' => StatutCandidature::REJETEE,
            'motif_rejet' => $motif
        ]);
    }

    public function annuler(): void
    {
        $this->update(['statut_candidature' => StatutCandidature::ANNULEE]);
    }

    public function hasPaiementValide(): bool
    {
        return $this->paiement && $this->paiement->statut === \App\Enums\StatutPaiement::VERIFIED;
    }
}
