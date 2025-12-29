<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\RegionCameroun;

class Ecole extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code_ecole',
        'libelle_ecole',
        'libelle_ecole_en',
        'region',
        'localisation',
        'logo_url',
        'bp_ecole',
        'email_ecole',
        'siteweb_ecole',
        'devise',
        'telephone_ecole',
        'embleme_ecole',
        'est_actif',
        // Champs pour les fichiers
        'logo_path',
        'logo_original_name',
        'embleme_path',
        'embleme_original_name',
        'header_frame_path',
        'header_frame_original_name',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function departements()
    {
        return $this->hasMany(Departement::class, 'ecole_id');
    }

    public function getRegionLabelAttribute()
    {
        return $this->region ? RegionCameroun::label($this->region) : null;
    }

    /**
     * Obtenir le chemin complet du logo pour les PDFs
     */
    public function getLogoFullPathAttribute()
    {
        if (!$this->logo_path) {
            return null;
        }
        
        $path = storage_path('app/public/' . $this->logo_path);
        
        if (!file_exists($path)) {
            return null;
        }
        
        // Convertir en base64 pour DomPDF
        $imageData = base64_encode(file_get_contents($path));
        $mimeType = mime_content_type($path);
        
        return 'data:' . $mimeType . ';base64,' . $imageData;
    }

    /**
     * Obtenir le chemin complet de l'emblème pour les PDFs
     */
    public function getEmblemeFullPathAttribute()
    {
        if (!$this->embleme_path) {
            return null;
        }
        
        $path = storage_path('app/public/' . $this->embleme_path);
        
        if (!file_exists($path)) {
            return null;
        }
        
        // Convertir en base64 pour DomPDF
        $imageData = base64_encode(file_get_contents($path));
        $mimeType = mime_content_type($path);
        
        return 'data:' . $mimeType . ';base64,' . $imageData;
    }

    /**
     * Obtenir le chemin complet du header frame pour les PDFs
     */
    public function getHeaderFrameFullPathAttribute()
    {
        if (!$this->header_frame_path) {
            return null;
        }
        
        $path = storage_path('app/public/' . $this->header_frame_path);
        
        if (!file_exists($path)) {
            return null;
        }
        
        // Convertir en base64 pour DomPDF
        $imageData = base64_encode(file_get_contents($path));
        $mimeType = mime_content_type($path);
        
        return 'data:' . $mimeType . ';base64,' . $imageData;
    }

    /**
     * Obtenir l'URL publique du logo
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    /**
     * Obtenir l'URL publique de l'emblème
     */
    public function getEmblemeUrlAttribute()
    {
        return $this->embleme_path ? asset('storage/' . $this->embleme_path) : null;
    }

    /**
     * Obtenir l'URL publique du header frame
     */
    public function getHeaderFrameUrlAttribute()
    {
        return $this->header_frame_path ? asset('storage/' . $this->header_frame_path) : null;
    }
}
