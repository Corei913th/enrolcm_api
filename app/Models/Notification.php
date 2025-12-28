<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'notifications';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'utilisateur_id',
        'type_notification',
        'titre',
        'message',
        'canal',
        'est_lue',
        'date_lecture',
        'est_envoyee',
        'date_envoi',
        'metadata',
        'priorite',
    ];

    protected $casts = [
        'est_lue' => 'boolean',
        'est_envoyee' => 'boolean',
        'date_lecture' => 'datetime',
        'date_envoi' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    // Scopes
    public function scopeNonLues($query)
    {
        return $query->where('est_lue', false);
    }

    public function scopeLues($query)
    {
        return $query->where('est_lue', true);
    }

    public function scopeNonEnvoyees($query)
    {
        return $query->where('est_envoyee', false);
    }

    public function scopeEnvoyees($query)
    {
        return $query->where('est_envoyee', true);
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type_notification', $type);
    }

    public function scopeParCanal($query, $canal)
    {
        return $query->where('canal', $canal);
    }

    public function scopeParPriorite($query, $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    public function scopeUrgentes($query)
    {
        return $query->where('priorite', 'urgente');
    }

    public function scopeRecentes($query, $jours = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($jours));
    }

    // Helpers
    public function marquerCommeLue()
    {
        $this->update([
            'est_lue' => true,
            'date_lecture' => now(),
        ]);
    }

    public function marquerCommeEnvoyee()
    {
        $this->update([
            'est_envoyee' => true,
            'date_envoi' => now(),
        ]);
    }

    public function isLue()
    {
        return $this->est_lue;
    }

    public function isEnvoyee()
    {
        return $this->est_envoyee;
    }

    public function isUrgente()
    {
        return $this->priorite === 'urgente';
    }

    public function getIcone()
    {
        $icones = [
            'CANDIDATURE_SOUMISE' => '📝',
            'CANDIDATURE_VALIDEE' => '✅',
            'CANDIDATURE_REJETEE' => '❌',
            'DOSSIER_INCOMPLET' => '⚠️',
            'CONVOCATION_DISPONIBLE' => '📄',
            'RAPPEL_EXAMEN' => '⏰',
            'RESULTATS_DISPONIBLES' => '📊',
            'ADMISSION' => '🎉',
            'ECHEC' => '😔',
            'LISTE_ATTENTE' => '⏳',
            'PAIEMENT_RECU' => '💰',
            'PAIEMENT_VALIDE' => '✔️',
            'PAIEMENT_REJETE' => '❌',
            'INFORMATION_GENERALE' => 'ℹ️',
            'ALERTE' => '🚨',
            'RAPPEL' => '🔔',
        ];

        return $icones[$this->type_notification] ?? '📬';
    }

    public function getCouleur()
    {
        $couleurs = [
            'CANDIDATURE_VALIDEE' => 'success',
            'CANDIDATURE_REJETEE' => 'danger',
            'ADMISSION' => 'success',
            'ECHEC' => 'danger',
            'ALERTE' => 'warning',
            'INFORMATION_GENERALE' => 'info',
        ];

        return $couleurs[$this->type_notification] ?? 'primary';
    }
}
