<?php

namespace App\DTOs\Auth;

use Spatie\LaravelData\Data;
use App\Http\Requests\Auth\ChangePasswordRequest;

class ChangePasswordDTO extends Data
{
    public function __construct(
        public readonly string $old_password,
        public readonly string $new_password,
    ) {}

    public static function fromRequest(ChangePasswordRequest $request): self
    {
        return new self(
            old_password: $request->validated('old_password'),
            new_password: $request->validated('new_password'),
        );
    }
}
