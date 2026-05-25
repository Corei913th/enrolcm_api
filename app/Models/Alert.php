<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperAlert
 */
class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidature_id',
        'type',
        'severity',
        'title',
        'message',
        'is_dismissed',
        'dismissed_at',
    ];

    protected $casts = [
        'is_dismissed' => 'boolean',
        'dismissed_at' => 'datetime',
    ];

    /**
     * Get the candidature that owns the alert.
     */
    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }
}
