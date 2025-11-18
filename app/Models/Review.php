<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Review extends Model implements Auditable
{
    use HasFactory, LogsActivity, AuditableTrait;

    protected $fillable = [
        'tenant_id',
        'reviewable_type',
        'reviewable_id',
        'reviewer_id',
        'action',
        'notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['action', 'notes', 'reviewed_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function createReview($reviewable, ?User $reviewer, string $action, ?string $notes = null): self
    {
        return self::create([
            'tenant_id' => $reviewable->tenant_id,
            'reviewable_type' => get_class($reviewable),
            'reviewable_id' => $reviewable->id,
            'reviewer_id' => $reviewer?->id,
            'action' => $action,
            'notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }
}