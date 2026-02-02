<?php

namespace App\Http\Requests\AdmissionRules;

use Illuminate\Foundation\Http\FormRequest;

class UpsertAdmissionRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seuil_admission_standard' => 'required|numeric|min:0|max:20',
            'seuil_admission_minimum' => 'required|numeric|min:0|max:20',
            'permet_admission_conditionnelle' => 'required|boolean',
            'pourcentage_places_conditionnelles' => 'required|integer|min:0|max:100',
            'criteres_prioritaires' => 'nullable|array',
            'criteres_prioritaires.*' => 'string|in:age,region,main_subjects',
            'quotas_regionaux' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'seuil_admission_standard.required' => 'Le seuil standard est requis',
            'seuil_admission_standard.min' => 'Le seuil standard doit être >= 0',
            'seuil_admission_standard.max' => 'Le seuil standard doit être <= 20',
            'seuil_admission_minimum.required' => 'Le seuil minimum est requis',
            'seuil_admission_minimum.min' => 'Le seuil minimum doit être >= 0',
            'seuil_admission_minimum.max' => 'Le seuil minimum doit être <= 20',
            'permet_admission_conditionnelle.required' => 'Ce champ est requis',
            'pourcentage_places_conditionnelles.required' => 'Le pourcentage est requis',
            'pourcentage_places_conditionnelles.min' => 'Le pourcentage doit être >= 0',
            'pourcentage_places_conditionnelles.max' => 'Le pourcentage doit être <= 100',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->seuil_admission_minimum >= $this->seuil_admission_standard) {
                $validator->errors()->add(
                    'seuil_admission_minimum',
                    'Le seuil minimum doit être inférieur au seuil standard'
                );
            }
        });
    }
}
