<?php

namespace App\Http\Requests\Resultats;

use Illuminate\Foundation\Http\FormRequest;

class CalculerResultatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Géré par le middleware
    }

    public function rules(): array
    {
        return [
            'force' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'force.boolean' => 'Le paramètre force doit être un booléen (true ou false)',
        ];
    }
}
