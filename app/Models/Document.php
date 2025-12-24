<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Document extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'documents';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'candidature_id',
        'fichier_url',
        'nom_original',
        'type_document',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    
    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'candidature_id');
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
}
