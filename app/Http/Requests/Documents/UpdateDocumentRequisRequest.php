<?php

namespace App\Http\Requests\Documents;

use App\Enums\TypeDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentRequisRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true; // TODO: Vérifier que l'utilisateur a les droits admin
  }

  public function rules(): array
  {
    return [
      'nom_document' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string', 'max:1000'],
      'type_document' => ['required', Rule::enum(TypeDocument::class)],
      'est_obligatoire' => ['boolean'],
      'format_accepte' => ['nullable', 'array', 'min:1'],
      'format_accepte.*' => ['string', 'in:pdf,jpg,jpeg,png,gif,bmp'],
      'taille_max_mb' => ['integer', 'min:1', 'max:50'],
      'ordre_affichage' => ['integer', 'min:0'],
      'est_actif' => ['boolean'],
    ];
  }

  public function messages(): array
  {
    return [
      'nom_document.required' => 'Le nom du document est obligatoire',
      'type_document.required' => 'Le type de document est obligatoire',
      'type_document.enum' => 'Le type de document n\'est pas valide',
      'format_accepte.*.in' => 'Format de fichier non supporté',
      'taille_max_mb.min' => 'La taille maximale doit être d\'au moins 1 MB',
      'taille_max_mb.max' => 'La taille maximale ne peut pas dépasser 50 MB',
    ];
  }
}
