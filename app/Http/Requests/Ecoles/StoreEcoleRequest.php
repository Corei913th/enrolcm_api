<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\RegionCameroun;

class StoreEcoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code_ecole' => 'required|string|unique:ecoles,code_ecole',
            'libelle_ecole' => 'required|string|max:255',
            'region' => ['required', new Enum(RegionCameroun::class)],
            'localisation' => 'nullable|string',
            'email_ecole' => 'nullable|email',
            'telephone_ecole' => 'nullable|string',
            'siteweb_ecole' => 'nullable|url',
            'devise' => 'nullable|string',
            'bp_ecole' => 'nullable|string',
            'logo_url' => 'nullable|string',
            'embleme_ecole' => 'nullable|string',
        ];
    }
}
