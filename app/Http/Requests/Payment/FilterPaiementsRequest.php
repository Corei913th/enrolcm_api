<?php

namespace App\Http\Requests\Payment;

use App\Enums\StatutPaiement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterPaiementsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statut' => ['sometimes', Rule::enum(StatutPaiement::class)],
            'concours_id' => ['sometimes', 'uuid', 'exists:concours,id'],
            'candidat_id' => ['sometimes', 'uuid', 'exists:candidats,utilisateur_id'],
            'reference' => ['sometimes', 'string', 'max:50'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'statut.enum' => 'Le statut doit être: EN_ATTENTE, OCR_VERIFIE, VALIDE ou REJETE',
            'concours_id.uuid' => 'L\'identifiant du concours est invalide',
            'concours_id.exists' => 'Le concours spécifié n\'existe pas',
            'candidat_id.uuid' => 'L\'identifiant du candidat est invalide',
            'candidat_id.exists' => 'Le candidat spécifié n\'existe pas',
            'reference.string' => 'La référence doit être une chaîne de caractères',
            'reference.max' => 'La référence ne peut pas dépasser 50 caractères',
            'per_page.integer' => 'Le nombre d\'éléments par page doit être un entier',
            'per_page.min' => 'Le nombre d\'éléments par page doit être au moins 1',
            'per_page.max' => 'Le nombre d\'éléments par page ne peut pas dépasser 100',
        ];
    }
}
