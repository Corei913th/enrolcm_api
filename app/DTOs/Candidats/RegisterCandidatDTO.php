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
        public readonly string $concoursId,
        public readonly string $sessionId
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
            concoursId: $data['concours_id'],
            sessionId: $data['session_id']
        );
    }
}
