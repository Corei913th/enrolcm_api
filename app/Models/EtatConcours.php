<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtatConcours extends Model
{
    use HasFactory;

    protected $table = 'etat_concours';
    protected $fillable = ['etat_libelle'];

    public function candidatures()
    {
        return $this->belongsToMany(Candidature::class, 'candidature_etat_concours');
    }
}
