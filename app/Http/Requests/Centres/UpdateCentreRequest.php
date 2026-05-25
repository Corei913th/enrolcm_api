<?php

namespace App\Http\Requests\Centres;

use App\Enums\RegionCameroun;
use App\Enums\TypeCentre;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateCentreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Récupérer l'ID du centre depuis la route
        $centreId = $this->route('centre');

        return [
            'libelle_centre' => [
                'sometimes',
                'required',
                'string',
                'max:200',
                Rule::unique('centres', 'libelle_centre')->ignore($centreId),
            ],
            'type_centre' => ['sometimes', 'required', new Enum(TypeCentre::class)],
            'ville_centre' => 'sometimes|required|string|max:100',
            'region_id' => 'sometimes|required|exists:regions,id',
            'region' => ['nullable', new Enum(RegionCameroun::class)],
            'departement' => 'nullable|string|max:100',
            'arrondissement' => 'nullable|string|max:100',
            'capacite' => 'sometimes|integer|min:0',
            'est_actif' => 'sometimes|boolean',
            'responsable_id' => 'nullable|exists:responsables_centre,utilisateur_id',
        ];
    }
}
