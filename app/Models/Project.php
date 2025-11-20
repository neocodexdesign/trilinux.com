<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Traits\BelongsToTenant;

class Project extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'status',
        'expected_start_at',
        'expected_end_at',
        'started_at',
        'ended_at',
        'created_by',
        'team_id',
        'responsible_id',
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



    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class)->orderBy('order');
    }

    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(Task::class, Stage::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable')->orderBy('created_at', 'desc');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable')->orderBy('created_at', 'desc');
    }

    public function start(): bool
    {
        if ($this->status !== 'planned') {
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
