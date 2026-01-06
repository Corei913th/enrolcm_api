<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $utilisateur_id
 * @property string $matricule
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Utilisateur $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereMatricule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereUtilisateurId($value)
 */
	class Admin extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $utilisateur_id
 * @property string|null $adresse_cand
 * @property string|null $nom_cand
 * @property string|null $prenom_cand
 * @property string $nationalite_cand
 * @property int|null $age_cand
 * @property \Illuminate\Support\Carbon|null $date_naissance_cand
 * @property string|null $nom_tuteur_cand
 * @property string|null $telephone_tuteur_cand
 * @property string|null $sexe_cand
 * @property string|null $ethnie_cand
 * @property string|null $nom_parent
 * @property string|null $telephone_parent
 * @property string|null $code_cand
 * @property string|null $niveau_scolaire
 * @property string|null $filiere_origine
 * @property string|null $diplome_admission
 * @property string|null $mention
 * @property \Illuminate\Support\Carbon|null $annee_diplome
 * @property string|null $numero_cni
 * @property \Illuminate\Support\Carbon|null $date_delivrance_cni
 * @property string|null $statut_matrimonial
 * @property string|null $nom_pere
 * @property string|null $telephone_pere
 * @property \App\Enums\RegionCameroun|null $region
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $est_actif
 * @property string|null $lieu_naissance_cand
 * @property string|null $departement
 * @property string|null $arrondissement
 * @property string|null $etablissement_origine
 * @property string|null $ville_etablissement
 * @property string|null $serie_bac
 * @property int|null $annee_obtention_bac
 * @property string|null $filiere_id
 * @property bool $a_handicap
 * @property string|null $type_handicap
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Candidature> $candidatures
 * @property-read int|null $candidatures_count
 * @property-read \App\Models\Filiere|null $filiere
 * @property-read mixed $email
 * @property-read mixed $telephone
 * @property-read mixed $username
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Paiement> $paiements
 * @property-read int|null $paiements_count
 * @property-read \App\Models\Utilisateur $utilisateur
 * @method static \Database\Factories\CandidatFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereAHandicap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereAdresseCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereAgeCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereAnneeDiplome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereAnneeObtentionBac($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereArrondissement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereCodeCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereDateDelivranceCni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereDateNaissanceCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereDepartement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereDiplomeAdmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereEtablissementOrigine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereEthnieCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereFiliereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereFiliereOrigine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereLieuNaissanceCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereMention($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereNationaliteCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereNiveauScolaire($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereNomCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereNomParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereNomPere($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereNomTuteurCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereNumeroCni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat wherePrenomCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereSerieBac($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereSexeCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereStatutMatrimonial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereTelephoneParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereTelephonePere($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereTelephoneTuteurCand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereTypeHandicap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereUtilisateurId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidat whereVilleEtablissement($value)
 */
	class Candidat extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $candidat_id
 * @property string $concours_id
 * @property string $session_id
 * @property \Illuminate\Support\Carbon $date_candidature
 * @property string|null $code_cand_temp
 * @property string|null $code_cand_def
 * @property string|null $qr_code
 * @property \Illuminate\Support\Carbon|null $date_inscription
 * @property \Illuminate\Support\Carbon|null $date_depot_physique
 * @property \Illuminate\Support\Carbon|null $date_validation
 * @property string|null $motif_rejet
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $centre_id
 * @property \App\Enums\StatutCandidature $statut_candidature
 * @property bool $documents_complets
 * @property bool $paiement_valide
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CandidatureSalle> $affectationsSalles
 * @property-read int|null $affectations_salles_count
 * @property-read \App\Models\Candidat $candidat
 * @property-read \App\Models\Centre|null $centre
 * @property-read \App\Models\Concours $concours
 * @property-read \App\Models\ConcoursSession $concoursSession
 * @property-read \App\Models\Convocation|null $convocation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \App\Models\EtatCandidature|null $etatActuel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EtatCandidature> $etatsCandidature
 * @property-read int|null $etats_candidature_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Note> $notes
 * @property-read int|null $notes_count
 * @property-read \App\Models\Paiement|null $paiement
 * @property-read \App\Models\ResultatFinal|null $resultatFinal
 * @property-read \App\Models\Session $session
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature actives()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature brouillons()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature byConcours($concoursId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature byConcoursSession($concoursId, $sessionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature bySession($sessionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature enAttente()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature rejetees()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature validees()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereCandidatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereCentreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereCodeCandDef($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereCodeCandTemp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereConcoursId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereDateCandidature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereDateDepotPhysique($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereDateInscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereDateValidation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereDocumentsComplets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereMotifRejet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature wherePaiementValide($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereStatutCandidature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature withoutTrashed()
 */
	class Candidature extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $candidature_id
 * @property string|null $salle_id
 * @property string|null $planning_epreuve_id
 * @property string|null $numero_place
 * @property bool $est_present
 * @property \Illuminate\Support\Carbon|null $heure_arrivee
 * @property string|null $observations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Candidature $candidature
 * @property-read \App\Models\PlanningEpreuve|null $planningEpreuve
 * @property-read \App\Models\SalleExamen|null $salle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle absents()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle parCandidature($candidatureId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle parSalle($salleId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle presents()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle whereCandidatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle whereEstPresent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle whereHeureArrivee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle whereNumeroPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle wherePlanningEpreuveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle whereSalleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatureSalle whereUpdatedAt($value)
 */
	class CandidatureSalle extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $libelle_centre
 * @property string|null $type_centre
 * @property string|null $ville_centre
 * @property int $capacite
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $responsable_id
 * @property \App\Enums\RegionCameroun|null $region
 * @property string|null $departement
 * @property string|null $arrondissement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CandidatureSalle> $affectations
 * @property-read int|null $affectations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Candidature> $candidatures
 * @property-read int|null $candidatures_count
 * @property-read \App\Models\ResponsableCentre|null $responsable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalleExamen> $salles
 * @property-read int|null $salles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre actif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre byArrondissement($arrondissement)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre byDepartement($departement)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre byRegion(\App\Enums\RegionCameroun $region)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre byVille($ville)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre dansLaRegion(\App\Enums\RegionCameroun $region, $departement = null, $arrondissement = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereArrondissement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereCapacite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereDepartement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereLibelleCentre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereResponsableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereTypeCentre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Centre whereVilleCentre($value)
 */
	class Centre extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $libelle_concours
 * @property \Illuminate\Support\Carbon $date_limite_depot
 * @property \Illuminate\Support\Carbon $date_examen
 * @property int $nbre_max_places
 * @property numeric $frais_inscription
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $spec_concours_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Candidature> $candidatures
 * @property-read int|null $candidatures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ConcoursSession> $concoursSessions
 * @property-read int|null $concours_sessions_count
 * @property-read \App\Models\ConcoursPaiement|null $configurationPaiement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EtatConcoursSession> $etatsConcours
 * @property-read int|null $etats_concours_count
 * @property-read \App\Models\ConcoursSession|\App\Models\ConcoursFiliere|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Filiere> $filieres
 * @property-read int|null $filieres_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Paiement> $paiements
 * @property-read int|null $paiements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Session> $sessions
 * @property-read int|null $sessions_count
 * @property-read \App\Models\SpecConcours|null $specConcours
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours whereDateExamen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours whereDateLimiteDepot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours whereFraisInscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours whereLibelleConcours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours whereNbreMaxPlaces($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours whereSpecConcoursId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concours whereUpdatedAt($value)
 */
	class Concours extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $concours_id
 * @property string $filiere_id
 * @property int $nombre_places
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $session_id
 * @property-read \App\Models\Concours $concours
 * @property-read \App\Models\Filiere $filiere
 * @property-read \App\Models\Session $session
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursFiliere newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursFiliere newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursFiliere query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursFiliere whereConcoursId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursFiliere whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursFiliere whereFiliereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursFiliere whereNombrePlaces($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursFiliere whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursFiliere whereUpdatedAt($value)
 */
	class ConcoursFiliere extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $concours_id
 * @property string $banque_nom
 * @property string $numero_compte
 * @property string $nom_beneficiaire
 * @property numeric $montant
 * @property \Illuminate\Support\Carbon $date_limite
 * @property string|null $instructions
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Concours $concours
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement actif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement nonExpire()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement whereBanqueNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement whereConcoursId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement whereDateLimite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement whereMontant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement whereNomBeneficiaire($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement whereNumeroCompte($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursPaiement whereUpdatedAt($value)
 */
	class ConcoursPaiement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $concours_id
 * @property string $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Candidature> $candidatures
 * @property-read int|null $candidatures_count
 * @property-read \App\Models\Concours $concours
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EtatConcoursSession> $etats
 * @property-read int|null $etats_count
 * @property-read \App\Models\Session $session
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursSession whereConcoursId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursSession whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcoursSession whereUpdatedAt($value)
 */
	class ConcoursSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $candidature_id
 * @property string $numero_convocation
 * @property string|null $qr_code
 * @property string|null $fichier_pdf_url
 * @property \Illuminate\Support\Carbon $date_generation
 * @property bool $est_telechargee
 * @property \Illuminate\Support\Carbon|null $date_telechargement
 * @property bool $est_envoyee
 * @property \Illuminate\Support\Carbon|null $date_envoi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Candidature $candidature
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation envoyees()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation nonEnvoyees()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation nonTelechargees()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation telechargees()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereCandidatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereDateEnvoi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereDateGeneration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereDateTelechargement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereEstEnvoyee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereEstTelechargee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereFichierPdfUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereNumeroConvocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Convocation whereUpdatedAt($value)
 */
	class Convocation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $utilisateur_id
 * @property string|null $specialite
 * @property string|null $matricule_enseignant
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Utilisateur $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Correcteur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Correcteur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Correcteur query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Correcteur whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Correcteur whereMatriculeEnseignant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Correcteur whereSpecialite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Correcteur whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Correcteur whereUtilisateurId($value)
 */
	class Correcteur extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $code_departement
 * @property string $libelle_departement
 * @property string|null $ecole_id
 * @property string|null $desc_departement
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecole|null $ecole
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Filiere> $filieres
 * @property-read int|null $filieres_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departement whereCodeDepartement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departement whereDescDepartement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departement whereEcoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departement whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departement whereLibelleDepartement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departement whereUpdatedAt($value)
 */
	class Departement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $candidature_id
 * @property string $fichier_url
 * @property string $nom_original
 * @property string $type_document
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Candidature $candidature
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document byCandidature($candidatureId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCandidatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFichierUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereNomOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereTypeDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUpdatedAt($value)
 */
	class Document extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $code_ecole
 * @property string $libelle_ecole
 * @property \App\Enums\RegionCameroun|null $region
 * @property string|null $localisation
 * @property string|null $logo_url
 * @property string|null $bp_ecole
 * @property string|null $email_ecole
 * @property string|null $siteweb_ecole
 * @property string|null $devise
 * @property string|null $telephone_ecole
 * @property string|null $embleme_ecole
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $libelle_ecole_en
 * @property string|null $nom_directeur
 * @property string $titre_directeur
 * @property string|null $nom_institution_tutelle
 * @property string|null $nom_institution_tutelle_en
 * @property string|null $numero_agrement
 * @property \Illuminate\Support\Carbon|null $date_creation
 * @property string|null $logo_institution_tutelle_url
 * @property string|null $adresse_complete
 * @property string|null $ville
 * @property string|null $fax
 * @property string|null $telephone_2
 * @property string|null $slogan
 * @property string|null $mentions_legales
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Departement> $departements
 * @property-read int|null $departements_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereAdresseComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereBpEcole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereCodeEcole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereDateCreation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereDevise($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereEmailEcole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereEmblemeEcole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereLibelleEcole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereLibelleEcoleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereLocalisation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereLogoInstitutionTutelleUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereMentionsLegales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereNomDirecteur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereNomInstitutionTutelle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereNomInstitutionTutelleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereNumeroAgrement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereSitewebEcole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereSlogan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereTelephone2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereTelephoneEcole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereTitreDirecteur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ecole whereVille($value)
 */
	class Ecole extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id_epreuve
 * @property string $intitule
 * @property string|null $session
 * @property string|null $url_epreuve
 * @property string $type_epreuve
 * @property int $duree_en_minute
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Note> $notes
 * @property-read int|null $notes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve actif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve bySession($session)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve whereDureeEnMinute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve whereIdEpreuve($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve whereIntitule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve whereSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve whereTypeEpreuve($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Epreuve whereUrlEpreuve($value)
 */
	class Epreuve extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $libelle_etat
 * @property string|null $desc_etat
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EtatCandidature> $etatCandidatures
 * @property-read int|null $etat_candidatures_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etat whereDescEtat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etat whereLibelleEtat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etat whereUpdatedAt($value)
 */
	class Etat extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $candidature_id
 * @property string|null $etat_id
 * @property \Illuminate\Support\Carbon $date_etat
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Candidature $candidature
 * @property-read \App\Models\Etat|null $etat
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatCandidature byCandidature($candidatureId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatCandidature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatCandidature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatCandidature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatCandidature recent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatCandidature whereCandidatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatCandidature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatCandidature whereDateEtat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatCandidature whereEtatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatCandidature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatCandidature whereUpdatedAt($value)
 */
	class EtatCandidature extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Candidature> $candidatures
 * @property-read int|null $candidatures_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcours newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcours newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcours query()
 */
	class EtatConcours extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string|null $concours_session_concours_id
 * @property string|null $concours_session_session_id
 * @property string|null $etat_session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Concours|null $concours
 * @property-read \App\Models\EtatSession|null $etatSession
 * @property-read \App\Models\Session|null $session
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcoursSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcoursSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcoursSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcoursSession recent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcoursSession whereConcoursSessionConcoursId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcoursSession whereConcoursSessionSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcoursSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcoursSession whereEtatSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcoursSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatConcoursSession whereUpdatedAt($value)
 */
	class EtatConcoursSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $libelle_etat
 * @property string|null $desc_etat
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatSession whereDescEtat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatSession whereLibelleEtat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EtatSession whereUpdatedAt($value)
 */
	class EtatSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $code_filiere
 * @property string $libelle_filiere
 * @property string|null $departement_id
 * @property string|null $desc_filiere
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Candidat> $candidats
 * @property-read int|null $candidats_count
 * @property-read \App\Models\ConcoursFiliere|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Concours> $concours
 * @property-read int|null $concours_count
 * @property-read \App\Models\Departement|null $departement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Niveau> $niveaux
 * @property-read int|null $niveaux_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Niveau> $niveauxPivot
 * @property-read int|null $niveaux_pivot_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Filiere newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Filiere newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Filiere query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Filiere whereCodeFiliere($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Filiere whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Filiere whereDepartementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Filiere whereDescFiliere($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Filiere whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Filiere whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Filiere whereLibelleFiliere($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Filiere whereUpdatedAt($value)
 */
	class Filiere extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $code_matiere
 * @property string $libelle_matiere
 * @property int|null $coefficient
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Niveau> $niveaux
 * @property-read int|null $niveaux_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matiere newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matiere newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matiere query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matiere whereCodeMatiere($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matiere whereCoefficient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matiere whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matiere whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matiere whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matiere whereLibelleMatiere($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matiere whereUpdatedAt($value)
 */
	class Matiere extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $code_niveau
 * @property string $libelle_niveau
 * @property string|null $filiere_id
 * @property int|null $ordre
 * @property string|null $desc_niveau
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Filiere|null $filiere
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Filiere> $filieres
 * @property-read int|null $filieres_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Matiere> $matieres
 * @property-read int|null $matieres_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereCodeNiveau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereDescNiveau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereFiliereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereLibelleNiveau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereOrdre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereUpdatedAt($value)
 */
	class Niveau extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $candidature_id
 * @property string $epreuve_id
 * @property numeric $valeur
 * @property \Illuminate\Support\Carbon $date_saisie
 * @property bool $est_definitive
 * @property bool $est_eliminatoire
 * @property string $statut
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Candidature $candidature
 * @property-read \App\Models\Epreuve $epreuve
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note byStatut($statut)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note definitives()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note eliminatoires()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note enAttenteSaisie()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereCandidatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereDateSaisie($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereEpreuveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereEstDefinitive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereEstEliminatoire($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereValeur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note withoutTrashed()
 */
	class Note extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $utilisateur_id
 * @property string $type_notification
 * @property string $titre
 * @property string $message
 * @property string $canal
 * @property bool $est_lue
 * @property \Illuminate\Support\Carbon|null $date_lecture
 * @property bool $est_envoyee
 * @property \Illuminate\Support\Carbon|null $date_envoi
 * @property array<array-key, mixed>|null $metadata
 * @property string $priorite
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Utilisateur $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification envoyees()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification lues()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification nonEnvoyees()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification nonLues()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification parCanal($canal)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification parPriorite($priorite)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification parType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification recentes($jours = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification urgentes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCanal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereDateEnvoi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereDateLecture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereEstEnvoyee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereEstLue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification wherePriorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTitre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTypeNotification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUtilisateurId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $candidat_id
 * @property string $concours_id
 * @property string $reference
 * @property numeric $montant
 * @property string $preuve_paiement
 * @property numeric|null $montant_ocr
 * @property \Illuminate\Support\Carbon|null $date_ocr
 * @property string|null $banque_ocr
 * @property string|null $reference_ocr
 * @property numeric|null $ocr_confidence
 * @property array<array-key, mixed>|null $ocr_raw_data
 * @property \App\Enums\StatutPaiement $statut
 * @property string|null $motif_rejet
 * @property \Illuminate\Support\Carbon|null $validated_at
 * @property string|null $validated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $candidature_id
 * @property-read \App\Models\Candidat $candidat
 * @property-read \App\Models\Candidature|null $candidature
 * @property-read \App\Models\Concours $concours
 * @property-read \App\Models\Utilisateur|null $validatedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement enAttente()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement ocrVerifie()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement parConcours(string $concoursId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement rejete()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement valide()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereBanqueOcr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereCandidatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereCandidatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereConcoursId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereDateOcr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereMontant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereMontantOcr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereMotifRejet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereOcrConfidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereOcrRawData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement wherePreuvePaiement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereReferenceOcr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereValidatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereValidatedBy($value)
 */
	class Paiement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string|null $candidat_id
 * @property string $numero_recu
 * @property string|null $banque
 * @property numeric $montant
 * @property \Illuminate\Support\Carbon|null $date_paiement
 * @property string $image_path
 * @property array<array-key, mixed>|null $ocr_data
 * @property string $statut_verification
 * @property string|null $motif_rejet
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property string|null $verified_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Candidat|null $candidat
 * @property-read \App\Models\Utilisateur|null $verifiedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt enAttente()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt rejete()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt verifie()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereBanque($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereCandidatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereDatePaiement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereMontant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereMotifRejet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereNumeroRecu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereOcrData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereStatutVerification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentReceipt whereVerifiedBy($value)
 */
	class PaymentReceipt extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $libelle_permission
 * @property string|null $desc_permission
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereDescPermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereLibellePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $epreuve_id
 * @property string $concours_id
 * @property string $session_id
 * @property \Illuminate\Support\Carbon $date_epreuve
 * @property \Illuminate\Support\Carbon $heure_debut
 * @property \Illuminate\Support\Carbon $heure_fin
 * @property string|null $instructions
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CandidatureSalle> $affectationsSalles
 * @property-read int|null $affectations_salles_count
 * @property-read \App\Models\Concours $concours
 * @property-read \App\Models\Epreuve $epreuve
 * @property-read \App\Models\Session $session
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve aVenir()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve actif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve parDate($date)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve whereConcoursId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve whereDateEpreuve($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve whereEpreuveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve whereHeureDebut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve whereHeureFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanningEpreuve whereUpdatedAt($value)
 */
	class PlanningEpreuve extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $utilisateur_id
 * @property string $code_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Utilisateur $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResponsableCentre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResponsableCentre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResponsableCentre query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResponsableCentre whereCodeAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResponsableCentre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResponsableCentre whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResponsableCentre whereUtilisateurId($value)
 */
	class ResponsableCentre extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $candidature_id
 * @property numeric $moyenne_generale
 * @property numeric $total_point
 * @property int|null $rang
 * @property string|null $decision
 * @property string|null $mention
 * @property bool $est_admis
 * @property \Illuminate\Support\Carbon|null $date_publication
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Candidature $candidature
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal admis()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal byDecision($decision)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal publies()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal topRangs($limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereCandidatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereDatePublication($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereEstAdmis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereMention($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereMoyenneGenerale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereRang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereTotalPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatFinal withoutTrashed()
 */
	class ResultatFinal extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $libelle_role
 * @property string|null $desc_role
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Utilisateur> $utilisateurs
 * @property-read int|null $utilisateurs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereLibelleRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $numero_salle
 * @property int $capacite
 * @property string|null $centre_id
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Centre|null $centre
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen actif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen byCentre($centreId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen whereCapacite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen whereCentreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen whereNumeroSalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalleExamen whereUpdatedAt($value)
 */
	class SalleExamen extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $libelle_session
 * @property string|null $desc_session
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Enums\StatutSession $statut_session
 * @property \Illuminate\Support\Carbon|null $date_ouverture_inscription
 * @property \Illuminate\Support\Carbon|null $date_fermeture_inscription
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Candidature> $candidatures
 * @property-read int|null $candidatures_count
 * @property-read \App\Models\ConcoursSession|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Concours> $concours
 * @property-read int|null $concours_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ConcoursSession> $concoursSessions
 * @property-read int|null $concours_sessions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EtatConcoursSession> $etatsSession
 * @property-read int|null $etats_session_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session actif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session byStatut(\App\Enums\StatutSession $statut)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session ouvertes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereDateFermetureInscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereDateOuvertureInscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereDescSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereLibelleSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereStatutSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereUpdatedAt($value)
 */
	class Session extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string|null $desc_infos_concours
 * @property numeric $montant_frais_depot
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $nom_spec
 * @property array<array-key, mixed>|null $documents_requis
 * @property int|null $age_minimum
 * @property int|null $age_maximum
 * @property array<array-key, mixed>|null $series_bac_acceptees
 * @property array<array-key, mixed>|null $nationalites_acceptees
 * @property bool $est_actif
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Concours> $concours
 * @property-read int|null $concours_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours actif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereAgeMaximum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereAgeMinimum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereDescInfosConcours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereDocumentsRequis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereMontantFraisDepot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereNationalitesAcceptees($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereNomSpec($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereSeriesBacAcceptees($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecConcours whereUpdatedAt($value)
 */
	class SpecConcours extends \Eloquent {}
}

namespace App\Models{
/**
 * Alias pour le modèle Utilisateur
 * Permet la compatibilité avec les packages Laravel qui attendent App\Models\User
 *
 * @property string $id
 * @property string $user_name
 * @property string|null $email
 * @property string $mot_de_passe
 * @property string|null $telephone
 * @property bool $est_actif
 * @property bool $email_verifie
 * @property string $type_utilisateur
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin|null $admin
 * @property-read \App\Models\Candidat|null $candidat
 * @property-read \App\Models\Correcteur|null $correcteur
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\ResponsableCentre|null $responsableCentre
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User actif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byType($type)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifie($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMotDePasse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTypeUtilisateur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserName($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $user_name
 * @property string|null $email
 * @property string $mot_de_passe
 * @property string|null $telephone
 * @property bool $est_actif
 * @property bool $email_verifie
 * @property string $type_utilisateur
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin|null $admin
 * @property-read \App\Models\Candidat|null $candidat
 * @property-read \App\Models\Correcteur|null $correcteur
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\ResponsableCentre|null $responsableCentre
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur actif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereEmailVerifie($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereEstActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereMotDePasse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereTypeUtilisateur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereUserName($value)
 */
	class Utilisateur extends \Eloquent {}
}

