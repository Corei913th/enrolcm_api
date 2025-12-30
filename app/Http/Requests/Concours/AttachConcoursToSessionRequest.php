<?php

namespace App\Http\Requests\Concours;

use Illuminate\Foundation\Http\FormRequest;

class AttachConcoursToSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required', 'string', 'uuid', 'exists:sessions,id'],
            'date_debut' => ['sometimes', 'date', 'after:today'],
            'date_limite_depot' => ['sometimes', 'date', 'after:date_debut'],
            'nombre_places' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'session_id.required' => 'L\'identifiant de session est obligatoire',
            'session_id.uuid' => 'L\'identifiant de session est invalide',
            'session_id.exists' => 'La session spécifiée n\'existe pas',
            'date_debut.after' => 'La date d\'examen doit être dans le futur',
            'date_limite_depot.after' => 'La date limite doit être après la date d\'examen',
            'nombre_places.min' => 'Le nombre de places doit être au moins 1',
        ];
    }

    /**
     * Validation métier personnalisée
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            // Si les deux dates sont fournies, vérifier cohérence
            if (!empty($data['date_limite_depot']) && !empty($data['date_debut'])) {
                if ($data['date_limite_depot'] <= $data['date_debut']) {
                    $validator->errors()->add('date_limite_depot', 'La date limite doit être après la date d\'examen');
                }
            }
        });
    }
}
