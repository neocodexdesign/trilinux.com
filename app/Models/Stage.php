<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Traits\BelongsToTenant;

class Stage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'name',
        'description',
        'status',
        'expected_start_at',
        'expected_end_at',
        'started_at',
        'ended_at',
        'dependent_stage_id',
        'responsible_id',
        'team_id',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'expected_start_at' => 'datetime',
            'expected_end_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function dependentStage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'dependent_stage_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('order');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable')->orderBy('created_at', 'desc');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable')->orderBy('created_at', 'desc');
    }

    public function canStart(): bool
    {
        if ($this->status !== 'planned') {
            return false;
        }

        if ($this->dependent_stage_id && $this->dependentStage?->status !== 'completed') {
            return false;
        }

        return true;
    }

    public function start(): bool
    {
        if (!$this->canStart()) {
            return false;
        }

        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return true;
    }

    public function pause(): bool
    {
        if ($this->status !== 'in_progress') {
            return false;
        }

        $this->update(['status' => 'paused']);
        return true;
    }

    public function resume(): bool
    {
        if ($this->status !== 'paused') {
            return false;
        }

        $this->update(['status' => 'in_progress']);
        return true;
    }

    public function complete(): bool
    {
        if (!in_array($this->status, ['in_progress', 'paused'])) {
            return false;
        }

        $this->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);

        return true;
    }
}