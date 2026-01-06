<?php

namespace App\DTOs\Candidats;

class RegisterCandidatDTO
{
    public function __construct(
        public readonly string $pru,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $email,
        public readonly string $telephone,
        public readonly string $password,
        public readonly ?string $concoursId = null, // Optionnel, récupéré depuis PRU
        public readonly ?string $sessionId = null    // Optionnel, récupéré automatiquement
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            pru: $data['pru'],
            nom: $data['nom'],
            prenom: $data['prenom'],
            email: $data['email'],
            telephone: $data['telephone'],
            password: $data['password'],
            concoursId: $data['concours_id'] ?? null,
            sessionId: $data['session_id'] ?? null
        );
    }
}
