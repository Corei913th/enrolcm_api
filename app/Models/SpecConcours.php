<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SpecConcours extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'specs_concours';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'desc_infos_concours',
        'carte_nationale_identite',
        'diplomes',
        'certificat_nationalite',
        'releve_notes',
        'acte_naissance',
        'photo',
        'montant_frais_depot',
    ];

    protected $casts = [
        'carte_nationale_identite' => 'boolean',
        'diplomes' => 'boolean',
        'certificat_nationalite' => 'boolean',
        'releve_notes' => 'boolean',
        'acte_naissance' => 'boolean',
        'photo' => 'boolean',
        'montant_frais_depot' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Helpers
    public function getDocumentsRequis()
    {
        $documents = [];
        
        if ($this->carte_nationale_identite) {
            $documents[] = 'Carte Nationale d\'Identité';
        }
        if ($this->diplomes) {
            $documents[] = 'Diplômes';
        }
        if ($this->certificat_nationalite) {
            $documents[] = 'Certificat de Nationalité';
        }
        if ($this->releve_notes) {
            $documents[] = 'Relevé de Notes';
        }
        if ($this->acte_naissance) {
            $documents[] = 'Acte de Naissance';
        }
        if ($this->photo) {
            $documents[] = 'Photo d\'identité';
        }
        
        return $documents;
    }

    public function getNombreDocumentsRequis()
    {
        return count($this->getDocumentsRequis());
    }

    public function hasFraisDepot()
    {
        return $this->montant_frais_depot > 0;
    }

    public function getMontantFormate()
    {
        return number_format($this->montant_frais_depot, 0, ',', ' ') . ' FCFA';
    }
}
