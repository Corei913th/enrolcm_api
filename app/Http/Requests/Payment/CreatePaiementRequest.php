<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'concours_id' => ['required', 'uuid', 'exists:concours,id'],
            'reference' => ['sometimes', 'string', 'max:50'],
            'montant' => ['sometimes', 'numeric', 'min:0'],
            'numero_compte' => ['sometimes', 'string', 'regex:/^6[0-9]{8}$/'], // Format 9 chiffres commençant par 6 (MTN/Orange)
            'preuve' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'numero_compte.regex' => 'Le numéro de compte doit comporter 9 chiffres et commencer par 6.',
            'preuve.required' => 'La preuve de paiement est obligatoire',
            'preuve.file' => 'La preuve doit être un fichier',
            'preuve.mimes' => 'La preuve doit être au format JPG, PNG ou PDF',
            'preuve.max' => 'La preuve ne doit pas dépasser 5MB',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $concoursId = $this->input('concours_id');
            // Ignorer si le concours n'est pas valide (déjà géré par les règles)
            if (!$concoursId || !\App\Models\Concours::where('id', $concoursId)->exists()) {
                return;
            }

            $concoursPaiement = \App\Models\ConcoursPaiement::where('concours_id', $concoursId)->first();

            if (!$concoursPaiement) {
                return; // Pas de config de paiement stricte, on laisse passer (ou on pourrait bloquer)
            }

            // 1. Validation du numéro de compte (si fourni et paiement manuel)
            $numeroCompte = $this->input('numero_compte');
            if ($numeroCompte && $concoursPaiement->numero_compte) {
                if ($numeroCompte !== $concoursPaiement->numero_compte) {
                    $validator->errors()->add(
                        'numero_compte',
                        "Le numéro de compte saisi ({$numeroCompte}) ne correspond pas au compte officiel du concours ({$concoursPaiement->numero_compte})."
                    );
                }
            }

            // 2. Validation du montant (si fourni)
            $montant = $this->input('montant');
            if ($montant !== null) {
                $fraisAttendus = $concoursPaiement->montantTotal(); // Montant + Frais
                // Tolérance de 0% ou minime ? Ici stricte >=
                if ($montant < $concoursPaiement->montant) {
                    $validator->errors()->add(
                        'montant',
                        "Le montant saisi ({$montant}) est inférieur au montant requis pour ce concours ({$concoursPaiement->montant})."
                    );
                }
            }
        });
    }
}
