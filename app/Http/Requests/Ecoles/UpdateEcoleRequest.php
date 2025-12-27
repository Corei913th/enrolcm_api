<?php

namespace App\Http\Requests;
namespace App\Http\Requests\Ecoles;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\RegionCameroun;

class UpdateEcoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code_ecole' => 'sometimes|string|unique:ecoles,code_ecole,' . $this->ecole,
            'libelle_ecole' => 'sometimes|string|max:255',
            'region' => ['sometimes', new Enum(RegionCameroun::class)],
            'localisation' => 'nullable|string',
            'email_ecole' => 'nullable|email',
            'telephone_ecole' => 'nullable|string',
            'siteweb_ecole' => 'nullable|url',
            'devise' => 'nullable|string',
            'bp_ecole' => 'nullable|string',
            'logo_url' => 'nullable|string',
            'embleme_ecole' => 'nullable|string',
            'est_actif' => 'boolean',
        ];
    }
}
