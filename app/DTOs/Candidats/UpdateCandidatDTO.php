<?php

namespace App\DTOs\Candidats;

use App\Http\Requests\Candidats\UpdateCandidatRequest;
use Spatie\LaravelData\Data;

class UpdateCandidatDTO extends Data
{
    public function __construct(
        public string $utilisateur_id,
        public ?string $adresse_cand = null,
        public ?string $nom_cand = null,
        public ?string $prenom_cand = null,
        public ?string $nationalite_cand = null,
        public ?int $age_cand = null,
        public ?string $date_naissance_cand = null,
        public ?string $nom_tuteur_cand = null,
        public ?string $telephone_tuteur_cand = null,
        public ?string $sexe_cand = null,
        public ?string $handicap = null,
        public ?string $ethnie_cand = null,
        public ?string $nom_parent = null,
        public ?string $telephone_parent = null,
        public ?string $code_cand = null,
        public ?string $niveau_scolaire = null,
        public ?string $filiere_origine = null,
        public ?string $diplome_admission = null,
        public ?string $mention = null,
        public ?string $annee_diplome = null,
        public ?string $numero_cni = null,
        public ?string $date_delivrance_cni = null,
        public ?string $statut_matrimonial = null,
        public ?string $nom_pere = null,
        public ?string $telephone_pere = null,
        public ?string $telephone_candidat = null,
        public ?string $region = null,
    ) {}

    public static function fromRequest(UpdateCandidatRequest $request): self
    {
        $validated = $request->validated();
        
        return new self(
            utilisateur_id: $request->user()->id,
            adresse_cand: $validated['adresse_cand'] ?? null,
            nom_cand: $validated['nom_cand'] ?? null,
            prenom_cand: $validated['prenom_cand'] ?? null,
            nationalite_cand: $validated['nationalite_cand'] ?? null,
            age_cand: isset($validated['age_cand']) ? (int) $validated['age_cand'] : null,
            date_naissance_cand: $validated['date_naissance_cand'] ?? null,
            nom_tuteur_cand: $validated['nom_tuteur_cand'] ?? null,
            telephone_tuteur_cand: $validated['telephone_tuteur_cand'] ?? null,
            sexe_cand: $validated['sexe_cand'] ?? null,
            handicap: $validated['handicap'] ?? null,
            ethnie_cand: $validated['ethnie_cand'] ?? null,
            nom_parent: $validated['nom_parent'] ?? null,
            telephone_parent: $validated['telephone_parent'] ?? null,
            code_cand: $validated['code_cand'] ?? null,
            niveau_scolaire: $validated['niveau_scolaire'] ?? null,
            filiere_origine: $validated['filiere_origine'] ?? null,
            diplome_admission: $validated['diplome_admission'] ?? null,
            mention: $validated['mention'] ?? null,
            annee_diplome: $validated['annee_diplome'] ?? null,
            numero_cni: $validated['numero_cni'] ?? null,
            date_delivrance_cni: $validated['date_delivrance_cni'] ?? null,
            statut_matrimonial: $validated['statut_matrimonial'] ?? null,
            nom_pere: $validated['nom_pere'] ?? null,
            telephone_pere: $validated['telephone_pere'] ?? null,
            telephone_candidat: $validated['telephone_candidat'] ?? null,
            region: $validated['region'] ?? null,
        );
    }

    /**
     * Convertir en tableau en excluant les valeurs null
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter(parent::toArray(), fn($value) => $value !== null);
    }
}
