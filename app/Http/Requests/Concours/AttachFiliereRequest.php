<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

class AttachFiliereRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filiere_id' => ['required', 'uuid', 'exists:filieres,id'],
            'nombre_places' => ['required', 'integer', 'min:1', 'max:100000'],
        ];
    }

    public function messages(): array
    {
        return [
            'filiere_id.required' => 'L\'identifiant de la filière est obligatoire',
            'filiere_id.uuid' => 'L\'identifiant de la filière est invalide',
            'filiere_id.exists' => 'La filière spécifiée n\'existe pas',
            'nombre_places.required' => 'Le nombre de places est obligatoire',
            'nombre_places.integer' => 'Le nombre de places doit être un entier',
            'nombre_places.min' => 'Le nombre de places doit être au moins 1',
            'nombre_places.max' => 'Le nombre de places ne peut pas dépasser 100 000',
        ];
    }
}
