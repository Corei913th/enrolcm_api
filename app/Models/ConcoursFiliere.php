<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperConcoursFiliere
 */
class ConcoursFiliere extends Pivot
{
    protected $table = 'concours_filiere';

    public $incrementing = false;

    protected $primaryKey = ['concours_id', 'session_id', 'filiere_id'];

    protected $fillable = [
        'concours_id',
        'session_id',
        'filiere_id',
        'nombre_places',
    ];

    protected $casts = [
        'nombre_places' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function concours()
    {
        return $this->belongsTo(Concours::class, 'concours_id');
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'filiere_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    // Helpers
    public function hasPlacesDisponibles()
    {
        return $this->nombre_places > 0;
    }

    public function getNombreCandidatures()
    {
        return Candidature::where('concours_id', $this->concours_id)
            ->where('session_id', $this->session_id)
            ->whereHas('candidat', function ($query) {
                $query->where('filiere_id', $this->filiere_id);
            })
            ->where('statut_candidature', 'VALIDE') // Uniquement les validées
            ->count();
    }

    public function getPlacesRestantes()
    {
        return max(0, $this->nombre_places - $this->getNombreCandidatures());
    }

    public function peutAccepterCandidature()
    {
        return $this->session?->accepteInscriptions() &&
            $this->getPlacesRestantes() > 0;
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (! is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param  mixed  $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}
