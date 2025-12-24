<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Enums\TypeUtilisateur;

class Utilisateur extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable, HasApiTokens;

    protected $table = 'utilisateurs';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'mot_de_passe',
        'telephone',
        'est_actif',
        'email_verifie',
        'type_utilisateur',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'email_verifie' => 'boolean',
        'email_verifie_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    // Override Laravel's default password field
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    // Override Laravel's email verification
    public function hasVerifiedEmail()
    {
        return $this->email_verifie;
    }

    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verifie' => true,
        ])->save();
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    // Relations
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'utilisateur_role');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'utilisateur_id');
    }

    public function candidat()
    {
        return $this->hasOne(Candidat::class, 'utilisateur_id');
    }

    public function responsableCentre()
    {
        return $this->hasOne(ResponsableCentre::class, 'utilisateur_id');
    }

    public function correcteur()
    {
        return $this->hasOne(Correcteur::class, 'utilisateur_id');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type_utilisateur', $type);
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->type_utilisateur === TypeUtilisateur::ADMIN;
    }

    public function isCandidat(): bool
    {
        return $this->type_utilisateur === TypeUtilisateur::CANDIDAT;
    }

    public function isCorrecteur(): bool
    {
        return $this->type_utilisateur === TypeUtilisateur::CORRECTEUR;
    }

    public function isResponsableCentre(): bool
    {
        return $this->type_utilisateur === TypeUtilisateur::RESPONSABLE_CENTRE;
    }

    public function hasRole($roleName): bool
    {
        return $this->roles()->where('nom_role', $roleName)->exists();
    }

    public function hasPermission($permissionName): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('nom_permission', $permissionName);
            })
            ->exists();
    }

    public function activate()
    {
        $this->update(['est_actif' => true]);
    }

    public function deactivate()
    {
        $this->update(['est_actif' => false]);
    }
}
