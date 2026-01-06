<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class FilterPaymentReceiptsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statut_verification' => ['sometimes', 'in:en_attente,verifie,rejete'],
            'candidat_id' => ['sometimes', 'uuid', 'exists:candidats,utilisateur_id'],
            'date_debut' => ['sometimes', 'date'],
            'date_fin' => ['sometimes', 'date', 'after_or_equal:date_debut'],
            'banque' => ['sometimes', 'string', 'max:255'],
            'numero_recu' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'statut_verification.in' => 'Le statut doit être: en_attente, verifie ou rejete',
            'candidat_id.uuid' => 'L\'identifiant du candidat doit être un UUID valide',
            'candidat_id.exists' => 'Le candidat spécifié n\'existe pas',
            'date_debut.date' => 'La date de début doit être une date valide',
            'date_fin.date' => 'La date de fin doit être une date valide',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début',
            'banque.string' => 'Le nom de la banque doit être une chaîne de caractères',
            'banque.max' => 'Le nom de la banque ne peut pas dépasser 255 caractères',
            'numero_recu.string' => 'Le numéro de reçu doit être une chaîne de caractères',
            'numero_recu.max' => 'Le numéro de reçu ne peut pas dépasser 255 caractères',
            'per_page.integer' => 'Le nombre d\'éléments par page doit être un entier',
            'per_page.min' => 'Le nombre d\'éléments par page doit être au moins 1',
            'per_page.max' => 'Le nombre d\'éléments par page ne peut pas dépasser 100',
        ];
    }
}
