<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class EtatConcoursSession extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'etat_concours_session';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'concours_session_concours_id',
        'concours_session_session_id',
        'etat_session_id',
        'date_etat',
    ];

    protected $casts = [
        'date_etat' => 'datetime',
        'created_at' => 'datetime',
    ];

    
    public function etatSession()
    {
        return $this->belongsTo(EtatSession::class, 'etat_session_id');
    }

    public function concours()
    {
        return $this->belongsTo(Concours::class, 'concours_session_concours_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'concours_session_session_id');
    }

    // Scopes
    public function scopeRecent($query)
    {
        return $query->orderBy('date_etat', 'desc');
    }

    // Helpers
    public function getLibelleEtat()
    {
        return $this->etatSession ? $this->etatSession->getLibelleLabel() : null;
    }
}
