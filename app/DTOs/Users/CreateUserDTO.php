<?php


namespace App\DTOs\Users;

use App\Http\Requests\Admin\Users\StoreUserRequest;

class CreateUserDTO
{
    public function __construct(
        public string $email,
        public string $user_name,
        public string $mot_de_passe,
        public string $telephone,
        public string $type_utilisateur,
        public ?string $matricule = null,
        public ?string $specialite = null,
        public ?string $matricule_enseignant = null,
        public ?string $code_agent = null
    ) {
    }

    public static function fromRequest(StoreUserRequest $request): self
    {
        $data = $request->validated();
        return new self(
            email: $data['email'] ?? '',
            user_name: $data['user_name'],
            mot_de_passe: $data['mot_de_passe'],
            telephone: $data['telephone'] ?? '',
            type_utilisateur: $data['type_utilisateur'],
            matricule: $data['matricule'] ?? null,
            specialite: $data['specialite'] ?? null,
            matricule_enseignant: $data['matricule_enseignant'] ?? null,
            code_agent: $data['code_agent'] ?? null
        );
    }
}