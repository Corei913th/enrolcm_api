<?php

namespace App\Models;

use App\Enums\StatutVerificationDocument;
use App\Enums\TypeDocument;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperDocument
 */
class Document extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'documents';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'candidature_id',
        'document_requis_id',
        'fichier_url',
        'nom_original',
        'type_document',
        'statut_verification',
        'commentaire_verification',
        'valide_par',
        'date_verification',
    ];

    protected $casts = [
        'statut_verification' => StatutVerificationDocument::class,
        'type_document' => TypeDocument::class,
        'date_verification' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'candidature_id');
    }

    public function documentRequis()
    {
        return $this->belongsTo(DocumentRequis::class, 'document_requis_id');
    }

    public function validePar()
    {
        return $this->belongsTo(Utilisateur::class, 'valide_par', 'id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type_document', $type);
    }

    public function scopeByCandidature($query, $candidatureId)
    {
        return $query->where('candidature_id', $candidatureId);
    }

    public function scopeByDocumentRequis($query, $documentRequisId)
    {
        return $query->where('document_requis_id', $documentRequisId);
    }

    public function scopeValides($query)
    {
        return $query->where('statut_verification', StatutVerificationDocument::VALIDE);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut_verification', StatutVerificationDocument::EN_ATTENTE);
    }

    public function scopeRejetes($query)
    {
        return $query->where('statut_verification', StatutVerificationDocument::REJETE);
    }

    // Helpers
    public function getExtension()
    {
        return pathinfo($this->nom_original, PATHINFO_EXTENSION);
    }

    public function getTaille()
    {
        if (file_exists(storage_path('app/' . $this->fichier_url))) {
            return filesize(storage_path('app/' . $this->fichier_url));
        }

        return 0;
    }

    public function getTailleFormatee()
    {
        $taille = $this->getTaille();

        if ($taille >= 1048576) {
            return round($taille / 1048576, 2) . ' MB';
        } elseif ($taille >= 1024) {
            return round($taille / 1024, 2) . ' KB';
        } else {
            return $taille . ' B';
        }
    }

    public function isPDF()
    {
        return strtolower($this->getExtension()) === 'pdf';
    }

    public function isImage()
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

        return in_array(strtolower($this->getExtension()), $imageExtensions);
    }

    // Gestion des statuts de vérification
    public function valider($userId, $commentaire = null)
    {
        $this->update([
            'statut_verification' => StatutVerificationDocument::VALIDE,
            'valide_par' => $userId,
            'date_verification' => now(),
            'commentaire_verification' => $commentaire,
        ]);
    }

    public function rejeter($userId, $commentaire)
    {
        $this->update([
            'statut_verification' => StatutVerificationDocument::REJETE,
            'valide_par' => $userId,
            'date_verification' => now(),
            'commentaire_verification' => $commentaire,
        ]);
    }

    public function marquerEnAttente()
    {
        $this->update([
            'statut_verification' => StatutVerificationDocument::EN_ATTENTE,
            'valide_par' => null,
            'date_verification' => null,
            'commentaire_verification' => null,
        ]);
    }

    public function isValide()
    {
        return $this->statut_verification === StatutVerificationDocument::VALIDE;
    }

    public function isRejete()
    {
        return $this->statut_verification === StatutVerificationDocument::REJETE;
    }

    public function estEnAttente()
    {
        return $this->statut_verification === StatutVerificationDocument::EN_ATTENTE || is_null($this->statut_verification);
    }
}
