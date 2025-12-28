<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CancelUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'temp_path' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'temp_path.required' => 'Le chemin du fichier temporaire est obligatoire',
            'temp_path.string' => 'Le chemin du fichier temporaire doit être une chaîne de caractères',
        ];
    }
}
