<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class PaymentReference extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'payment_references';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'concours_id',
        'candidat_id',
        'reference',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function concours()
    {
        return $this->belongsTo(Concours::class, 'concours_id');
    }

    public function candidat()
    {
        return $this->belongsTo(Candidat::class, 'candidat_id', 'utilisateur_id');
    }

    // Scopes
    public function scopeValide($query)
    {
        return $query->whereNull('used_at')
            ->where('expires_at', '>', now());
    }

    public function scopeExpire($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeUtilise($query)
    {
        return $query->whereNotNull('used_at');
    }

    // Helpers
    public function isValide(): bool
    {
        return is_null($this->used_at) && $this->expires_at > now();
    }

    public function isExpire(): bool
    {
        return $this->expires_at <= now();
    }

    public function isUtilise(): bool
    {
        return !is_null($this->used_at);
    }

    public function marquerUtilise(): void
    {
        $this->update(['used_at' => now()]);
    }

    /**
     * Générer une référence unique
     */
    public static function genererReference(string $concoursId, string $candidatId): string
    {
        $prefix = 'PRU';
        $concoursCode = substr($concoursId, 0, 4);
        $candidatCode = substr($candidatId, 0, 4);
        $random = strtoupper(Str::random(6));
        
        return "{$prefix}-{$concoursCode}-{$candidatCode}-{$random}";
    }
}
