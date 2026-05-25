<?php

namespace App\Models;

use App\Enums\Genre;
use App\Enums\Langue;
use App\Enums\NiveauScolaire;
use App\Enums\RegionCameroun;
use App\Enums\SerieBac;
use App\Enums\StatutMatrimonial;
use App\Enums\TypeDiplome;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCandidat
 */
class Candidat extends Model
{
    use HasFactory;

    protected $primaryKey = 'utilisateur_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'utilisateur_id',
        'adresse_cand',
        'nom_cand',
        'prenom_cand',
        'nationalite_cand',
        'age_cand',
        'date_naissance_cand',
        'lieu_naissance_cand',
        'nom_tuteur_cand',
        'telephone_tuteur_cand',
        'sexe_cand',
        'a_handicap',
        'type_handicap',
        'ethnie_cand',
        'nom_parent',
        'telephone_parent',
        'code_cand',
        'filiere_id',
        'niveau_scolaire',
        'filiere_origine',
        'etablissement_origine',
        'ville_etablissement',
        'diplome_admission',
        'serie_bac',
        'annee_obtention_bac',
        'mention',
        'annee_diplome',
        'numero_cni',
        'date_delivrance_cni',
        'statut_matrimonial',
        'nom_pere',
        'telephone_pere',
        'region',
        'departement',
        'arrondissement',
        'premiere_langue',
        'autre_langue',
    ];

    protected $hidden = [
        'utilisateur_id',
    ];

    protected $casts = [
        'date_naissance_cand' => 'date',
        'annee_diplome' => 'date',
        'date_delivrance_cni' => 'date',
        'a_handicap' => 'boolean',
        'annee_obtention_bac' => 'integer',
        'region' => RegionCameroun::class,
        'premiere_langue' => Langue::class,
        'statut_matrimonial' => StatutMatrimonial::class,
        'sexe_cand' => Genre::class,
        'diplome_admission' => TypeDiplome::class,
        'serie_bac' => SerieBac::class,
        'niveau_scolaire' => NiveauScolaire::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'candidat_id', 'utilisateur_id');
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'filiere_id');
    }

    public function paiements()
    {
        return $this->hasManyThrough(Paiement::class, Candidature::class, 'candidat_id', 'candidature_id', 'utilisateur_id', 'id');
    }

    public function getFullName()
    {
        return "{$this->nom_cand} {$this->prenom_cand}";
    }

    // ACCÈS TRANSPARENT aux données utilisateur
    public function getTelephoneAttribute()
    {
        return $this->utilisateur?->telephone;
    }

    public function getEmailAttribute()
    {
        return $this->utilisateur?->email;
    }

    public function getUsernameAttribute()
    {
        return $this->utilisateur?->user_name;
    }

    // Vérifications de statut
    public function estActif(): bool
    {
        return $this->utilisateur?->est_actif ?? false;
    }

    public function emailVerifie(): bool
    {
        return $this->utilisateur?->email_verifie ?? false;
    }

    public function getPremiereLangueLibelle(): string
    {
        if ($this->premiere_langue === Langue::AUTRE && $this->autre_langue) {
            return $this->autre_langue;
        }

        return $this->premiere_langue?->label() ?? 'Non spécifié';
    }

    public function getStatutMatrimonialLibelle(): string
    {
        return $this->statut_matrimonial?->label() ?? 'Non spécifié';
    }
}
