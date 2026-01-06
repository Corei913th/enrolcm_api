<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurerPaiementConcoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'banque_nom' => ['required', 'string', 'max:100'],
            'numero_compte' => ['required', 'string', 'max:50'],
            'nom_beneficiaire' => ['required', 'string', 'max:200'],
            'montant' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'date_limite' => ['required', 'date', 'after:today'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'est_actif' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'banque_nom.required' => 'Le nom de la banque est obligatoire',
            'banque_nom.max' => 'Le nom de la banque ne peut pas dépasser 100 caractères',
            'numero_compte.required' => 'Le numéro de compte est obligatoire',
            'numero_compte.max' => 'Le numéro de compte ne peut pas dépasser 50 caractères',
            'nom_beneficiaire.required' => 'Le nom du bénéficiaire est obligatoire',
            'nom_beneficiaire.max' => 'Le nom du bénéficiaire ne peut pas dépasser 200 caractères',
            'montant.required' => 'Le montant est obligatoire',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être positif',
            'montant.max' => 'Le montant est trop élevé',
            'date_limite.required' => 'La date limite est obligatoire',
            'date_limite.date' => 'La date limite doit être une date valide',
            'date_limite.after' => 'La date limite doit être dans le futur',
            'instructions.max' => 'Les instructions ne peuvent pas dépasser 2000 caractères',
            'est_actif.boolean' => 'Le statut actif doit être vrai ou faux',
        ];
    }
}
