<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Convocation extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'convocations';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'candidature_id',
        'numero_convocation',
        'centre_id',
        'salle_id',
        'numero_place',
        'qr_code',
        'fichier_pdf_url',
        'date_generation',
        'est_telechargee',
        'date_telechargement',
        'est_envoyee',
        'date_envoi',
    ];

    protected $casts = [
        'date_generation' => 'datetime',
        'date_telechargement' => 'datetime',
        'date_envoi' => 'datetime',
        'est_telechargee' => 'boolean',
        'est_envoyee' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'candidature_id');
    }

    // Relations transitives (via candidature_salle)
    public function affectationsSalles()
    {
        return $this->candidature->affectationsSalles();
    }

    public function getCentre()
    {
        $affectation = $this->candidature->affectationsSalles()->first();
        return $affectation?->salle?->centre;
    }

    public function getSalle()
    {
        $affectation = $this->candidature->affectationsSalles()->first();
        return $affectation?->salle;
    }

    public function getNumeroPlace()
    {
        $affectation = $this->candidature->affectationsSalles()->first();
        return $affectation?->numero_place;
    }

    // Scopes
    public function scopeTelechargees($query)
    {
        return $query->where('est_telechargee', true);
    }

    public function scopeNonTelechargees($query)
    {
        return $query->where('est_telechargee', false);
    }

    public function scopeEnvoyees($query)
    {
        return $query->where('est_envoyee', true);
    }

    public function scopeNonEnvoyees($query)
    {
        return $query->where('est_envoyee', false);
    }

    // Helpers
    public function marquerTelechargee()
    {
        $this->update([
            'est_telechargee' => true,
            'date_telechargement' => now(),
        ]);
    }

    public function marquerEnvoyee()
    {
        $this->update([
            'est_envoyee' => true,
            'date_envoi' => now(),
        ]);
    }

    public function genererNumeroConvocation()
    {
        // Format: CONV-ANNEE-NUMERO (ex: CONV-2024-000123)
        $annee = now()->year;
        $dernier = static::whereYear('created_at', $annee)->count() + 1;
        return sprintf('CONV-%d-%06d', $annee, $dernier);
    }

    public function getStatutTelechargement()
    {
        return $this->est_telechargee ? 'Téléchargée' : 'Non téléchargée';
    }

    public function getStatutEnvoi()
    {
        return $this->est_envoyee ? 'Envoyée' : 'Non envoyée';
    }
}
