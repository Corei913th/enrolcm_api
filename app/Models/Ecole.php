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
        'adresse_complete',
        'ville',
        'logo_url',
        'logo_institution_tutelle_url',
        'bp_ecole',
        'email_ecole',
        'siteweb_ecole',
        'telephone_ecole',
        'fax',
        'telephone_2',
        'devise',
        'slogan',
        'embleme_ecole',
        'mentions_legales',
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
        'date_creation' => 'date',
        'est_actif' => 'boolean',
        'region' => RegionCameroun::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function departements()
    {
        return $this->hasMany(Departement::class, 'ecole_id');
    }

    public function getRegionLabel()
    {
        return $this->region?->label();
    }

    // Helpers pour génération de documents
    public function getAdresseComplete()
    {
        $parts = array_filter([
            $this->adresse_complete,
            $this->bp_ecole ? "BP: {$this->bp_ecole}" : null,
            $this->ville,
            $this->region?->label(),
        ]);

        return implode(', ', $parts);
    }

    public function getContactsComplets()
    {
        $contacts = [];

        if ($this->telephone_ecole) {
            $contacts[] = "Tél: {$this->telephone_ecole}";
        }

        if ($this->telephone_2) {
            $contacts[] = $this->telephone_2;
        }

        if ($this->fax) {
            $contacts[] = "Fax: {$this->fax}";
        }

        if ($this->email_ecole) {
            $contacts[] = "Email: {$this->email_ecole}";
        }

        if ($this->siteweb_ecole) {
            $contacts[] = "Web: {$this->siteweb_ecole}";
        }

        return implode(' | ', $contacts);
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
