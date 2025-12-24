<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'nom_tuteur_cand',
        'telephone_tuteur_cand',
        'sexe_cand',
        'handicap',
        'ethnie_cand',
        'nom_parent',
        'telephone_parent',
        'code_cand',
        'niveau_scolaire',
        'filiere_origine',
        'diplome_admission',
        'mention',
        'annee_diplome',
        'numero_cni',
        'date_delivrance_cni',
        'statut_matrimonial',
        'nom_pere',
        'telephone_pere',
        'numero_recu',
        'telephone_candidat',
    ];

    protected $casts = [
        'date_naissance_cand' => 'date',
        'annee_diplome' => 'date',
        'date_delivrance_cni' => 'date',
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

    public function getNomCompletAttribute()
    {
        return "{$this->nom_cand} {$this->prenom_cand}";
    }
}
