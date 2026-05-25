<?php

namespace App\Http\Requests\Documents;

use App\Enums\TypeDocument;
use App\Models\Candidature;
use App\Models\DocumentRequis;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Vérifier que l'utilisateur est propriétaire de la candidature
        $candidatureId = $this->input('candidature_id');
        $candidature = Candidature::find($candidatureId);

        return $candidature && $candidature->candidat_id === $this->user()->candidat->utilisateur_id;
    }

    public function rules(): array
    {
        return [
            'candidature_id' => ['required', 'uuid', 'exists:candidatures,id'],
            'document_requis_id' => ['required', 'uuid', 'exists:documents_requis,id'],
            'fichier' => [
                'required',
                'file',
                'max:' . (5 * 1024), // 5MB par défaut
                'mimes:pdf,jpg,jpeg,png,gif,bmp',
            ],
            'type_document' => ['required', Rule::enum(TypeDocument::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'candidature_id.required' => 'La candidature est obligatoire',
            'candidature_id.exists' => 'La candidature spécifiée n\'existe pas',
            'document_requis_id.required' => 'Le document requis est obligatoire',
            'document_requis_id.exists' => 'Le document requis n\'existe pas',
            'fichier.required' => 'Le fichier est obligatoire',
            'fichier.file' => 'Le fichier doit être un fichier valide',
            'fichier.max' => 'Le fichier ne doit pas dépasser 5 MB',
            'fichier.mimes' => 'Le fichier doit être au format PDF, JPG, JPEG, PNG, GIF ou BMP',
            'type_document.required' => 'Le type de document est obligatoire',
            'type_document.enum' => 'Le type de document n\'est pas valide',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $documentRequisId = $this->input('document_requis_id');
            $candidatureId = $this->input('candidature_id');

            if ($documentRequisId && $candidatureId) {
                $candidature = Candidature::find($candidatureId);
                $documentRequis = DocumentRequis::find($documentRequisId);

                if ($candidature && $documentRequis) {
                    if ($documentRequis->concours_id !== $candidature->concours_id) {
                        $validator->errors()->add('document_requis_id', 'Ce document n\'est pas requis pour ce concours');
                    }
                }
            }
        });
    }
}
