<?php

namespace App\Http\Requests\Centres;

use App\Enums\RegionCameroun;
use App\Enums\TypeCentre;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateCentreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'libelle_centre' => 'required|string|max:200|unique:centres,libelle_centre',
            'type_centre' => ['required', new Enum(TypeCentre::class)],
            'ville_centre' => 'required|string|max:100',
            'region_id' => 'required|exists:regions,id',
            'region' => ['nullable', new Enum(RegionCameroun::class)],
            'departement' => 'nullable|string|max:100',
            'arrondissement' => 'nullable|string|max:100',
            'capacite' => 'nullable|integer|min:0',
            'est_actif' => 'nullable|boolean',
            'responsable_id' => 'nullable|exists:responsables_centre,utilisateur_id',
        ];
    }
}
