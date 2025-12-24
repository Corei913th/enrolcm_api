<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correcteur extends Model
{
    use HasFactory;
    
    protected $table = 'correcteurs';
    protected $primaryKey = 'utilisateur_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'utilisateur_id',
        'specialite',
        'matricule_enseignant',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
