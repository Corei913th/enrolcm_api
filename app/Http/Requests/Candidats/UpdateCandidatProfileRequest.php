<?php

namespace App\Http\Requests\Candidats;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Genre;
use App\Enums\RegionCameroun;
use App\Enums\SerieBac;
use App\Enums\StatutMatrimonial;

class UpdateCandidatProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $candidatId = $this->user()->id;

        return [
            'adresse_cand' => 'sometimes|required|string|max:255',
            'nom_cand' => 'sometimes|required|string|max:100',
            'prenom_cand' => 'sometimes|required|string|max:100',
            'date_naissance_cand' => 'sometimes|required|date|before:today|after:1950-01-01',
            'lieu_naissance_cand' => 'nullable|string|max:100',

            // Localisation
            'region' => 'sometimes|required|in:' . implode(',', RegionCameroun::values()),
            'departement' => 'nullable|string|max:100',
            'arrondissement' => 'nullable|string|max:100',

            // Handicap
            'a_handicap' => 'nullable|boolean',
            'type_handicap' => 'nullable|string|max:255|required_if:a_handicap,true',


            'nom_tuteur_cand' => 'sometimes|required|string|max:100',
            'telephone_tuteur_cand' => [
                'sometimes',
                'required',
                'string',
                'regex:/^(6[5-9]\d{7}|2[2-3]\d{7})$/',
                'required_if:nom_tuteur_cand,!=,null',
            ],

            'sexe_cand' => 'sometimes|required|in:' . implode(',', Genre::values()),
            'ethnie_cand' => 'sometimes|required|string|max:100',


            'nom_parent' => 'sometimes|required|string|max:100',
            'telephone_parent' => [
                'sometimes',
                'required',
                'string',
                'regex:/^(6[5-9]\d{7}|2[2-3]\d{7})$/',
                'required_if:nom_parent,!=,null',
            ],

            // Informations académiques
            'niveau_scolaire' => 'nullable|string|max:100',
            'filiere_origine' => 'nullable|string|max:100',
            'etablissement_origine' => 'nullable|string|max:200',
            'ville_etablissement' => 'nullable|string|max:100',
            'diplome_admission' => 'nullable|string|max:100',
            'serie_bac' => 'nullable|string|max:20|in:' . implode(',', SerieBac::values()),
            'annee_obtention_bac' => [
                'nullable',
                'integer',
                'min:1980',
                'max:' . date('Y'),
            ],
            'mention' => 'nullable|string|max:100',
            'annee_diplome' => [
                'nullable',
                'integer',
                'min:1980',
                'max:' . date('Y'),
                'required_if:diplome_admission,!=,null',
            ],

            // Choix de filière
            'filiere_id' => 'nullable|uuid|exists:filieres,id',


            'numero_cni' => [
                'sometimes',
                'required',
                'string',
                'regex:/^[A-Z0-9]{7,17}$/',
                \Illuminate\Validation\Rule::unique('candidats', 'numero_cni')->ignore($candidatId, 'utilisateur_id'),
            ],
            'date_delivrance_cni' => [
                'sometimes',
                'required',
                'date',
                'before_or_equal:today',
                'after:1990-01-01',
                'required_if:numero_cni,!=,null',
            ],

            'telephone' => [
                'sometimes',
                'required',
                'string',
                'regex:/^(6[5-9]\d{7}|2[2-3]\d{7})$/',
                \Illuminate\Validation\Rule::unique('utilisateurs', 'telephone')->ignore($candidatId, 'id'),
            ],


            'nom_pere' => 'sometimes|required|string|max:100',
            'telephone_pere' => [
                'sometimes',
                'required',
                'string',
                'regex:/^(6[5-9]\d{7}|2[2-3]\d{7})$/',
                'required_if:nom_pere,!=,null',
            ],

            'nationalite_cand' => 'sometimes|required|string|max:50',
            'statut_matrimonial' => 'nullable|in:' . implode(',', StatutMatrimonial::values()),
        ];
    }

    public function messages()
    {
        return [
            // Localisation
            'lieu_naissance_cand.max' => 'Le lieu de naissance ne peut pas dépasser 100 caractères',
            'departement.max' => 'Le département ne peut pas dépasser 100 caractères',
            'arrondissement.max' => 'L\'arrondissement ne peut pas dépasser 100 caractères',

            // Handicap
            'a_handicap.boolean' => 'Le champ handicap doit être vrai ou faux',
            'type_handicap.required_if' => 'Le type de handicap est obligatoire si vous déclarez un handicap',

            // Académique
            'etablissement_origine.max' => 'Le nom de l\'établissement ne peut pas dépasser 200 caractères',
            'ville_etablissement.max' => 'La ville de l\'établissement ne peut pas dépasser 100 caractères',
            'serie_bac.max' => 'La série du bac ne peut pas dépasser 10 caractères',
            'annee_obtention_bac.integer' => 'L\'année d\'obtention du bac doit être un nombre',
            'annee_obtention_bac.min' => 'L\'année d\'obtention du bac doit être supérieure à 1980',
            'annee_obtention_bac.max' => 'L\'année d\'obtention du bac ne peut pas être dans le futur',

            // Filière
            'filiere_id.uuid' => 'L\'identifiant de la filière n\'est pas valide',
            'filiere_id.exists' => 'La filière sélectionnée n\'existe pas',

            // Téléphones
            'telephone.required' => 'Le numéro de téléphone est obligatoire',
            'telephone.regex' => 'Le numéro de téléphone doit être un numéro camerounais valide (ex: 655123456)',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé',

            'telephone_tuteur_cand.required' => 'Le numéro de téléphone du tuteur est obligatoire',
            'telephone_tuteur_cand.required_if' => 'Le numéro de téléphone du tuteur est obligatoire si vous renseignez le nom du tuteur',
            'telephone_tuteur_cand.regex' => 'Le numéro de téléphone du tuteur doit être un numéro camerounais valide',

            'telephone_parent.required' => 'Le numéro de téléphone du parent est obligatoire',
            'telephone_parent.required_if' => 'Le numéro de téléphone du parent est obligatoire si vous renseignez le nom du parent',
            'telephone_parent.regex' => 'Le numéro de téléphone du parent doit être un numéro camerounais valide',

            'telephone_pere.required' => 'Le numéro de téléphone du père est obligatoire',
            'telephone_pere.required_if' => 'Le numéro de téléphone du père est obligatoire si vous renseignez le nom du père',
            'telephone_pere.regex' => 'Le numéro de téléphone du père doit être un numéro camerounais valide',

            // Informations personnelles
            'adresse_cand.required' => 'L\'adresse du candidat est obligatoire',
            'nom_cand.required' => 'Le nom du candidat est obligatoire',
            'prenom_cand.required' => 'Le prénom du candidat est obligatoire',
            'sexe_cand.required' => 'Le sexe du candidat est obligatoire',
            'sexe_cand.in' => 'Le sexe doit être Masculin ou Féminin',

            // Dates
            'date_naissance_cand.required' => 'La date de naissance est obligatoire',
            'date_naissance_cand.before' => 'La date de naissance doit être antérieure à aujourd\'hui',
            'date_naissance_cand.after' => 'La date de naissance doit être postérieure à 1950',

            'date_delivrance_cni.required' => 'La date de délivrance de la CNI est obligatoire',
            'date_delivrance_cni.required_if' => 'La date de délivrance de la CNI est obligatoire si vous renseignez le numéro de CNI',
            'date_delivrance_cni.before_or_equal' => 'La date de délivrance ne peut pas être dans le futur',

            // CNI
            'numero_cni.required' => 'Le numéro de CNI est obligatoire',
            'numero_cni.regex' => 'Le numéro de CNI doit contenir entre 7 et 17 caractères alphanumériques',
            'numero_cni.unique' => 'Ce numéro de CNI est déjà utilisé',

            // Diplôme
            'diplome_admission.in' => 'Le diplôme sélectionné n\'est pas valide',
            'annee_diplome.integer' => 'L\'année du diplôme doit être un nombre',
            'annee_diplome.min' => 'L\'année du diplôme doit être supérieure à 1980',
            'annee_diplome.max' => 'L\'année du diplôme ne peut pas être dans le futur',
            'annee_diplome.required_if' => 'L\'année du diplôme est obligatoire si vous renseignez le diplôme d\'admission',

            // Région
            'region.required' => 'La région est obligatoire',
            'region.in' => 'La région sélectionnée n\'est pas valide',

            // Autres
            'ethnie_cand.required' => 'L\'ethnie du candidat est obligatoire',
            'nom_tuteur_cand.required' => 'Le nom du tuteur est obligatoire',
            'nom_parent.required' => 'Le nom du parent est obligatoire',
            'nom_pere.required' => 'Le nom du père est obligatoire',
        ];
    }
}
