<?php

namespace App\Models;

use App\Enums\TypeDocument;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperDocumentRequis
 */
class DocumentRequis extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'documents_requis';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'concours_id',
        'nom_document',
        'description',
        'type_document',
        'est_obligatoire',
        'format_accepte',
        'taille_max_mb',
        'est_actif',
        'ordre_affichage',
    ];

    protected $casts = [
        'est_obligatoire' => 'boolean',
        'format_accepte' => 'array',
        'taille_max_mb' => 'integer',
        'est_actif' => 'boolean',
        'ordre_affichage' => 'integer',
        'type_document' => TypeDocument::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function concours()
    {
        return $this->belongsTo(Concours::class, 'concours_id');
    }

    public function documentsSoumis()
    {
        return $this->hasMany(Document::class, 'document_requis_id');
    }

    // Scopes
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeObligatoires($query)
    {
        return $query->where('est_obligatoire', true);
    }

    public function scopeByConcours($query, $concoursId)
    {
        return $query->where('concours_id', $concoursId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('ordre_affichage');
    }

    // Helpers
    public function getFormatsAcceptesString()
    {
        return implode(', ', $this->format_accepte ?? []);
    }

    public function validerFichier($fichier)
    {
        $errors = [];

        // Vérifier l'extension
        $extension = strtolower($fichier->getClientOriginalExtension());
        if (! in_array($extension, $this->format_accepte ?? [])) {
            $errors[] = "Format {$extension} non accepté. Formats acceptés : " . $this->getFormatsAcceptesString();
        }

        // Vérifier la taille
        $tailleMB = $fichier->getSize() / 1024 / 1024;
        if ($tailleMB > $this->taille_max_mb) {
            $errors[] = "Fichier trop volumineux. Taille maximale : {$this->taille_max_mb} MB";
        }

        return $errors;
    }
}
