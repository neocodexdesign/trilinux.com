<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use App\Traits\BelongsToTenant;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'stage_id',
        'name',
        'description',
        'status',
        'expected_start_at',
        'expected_end_at',
        'started_at',
        'ended_at',
        'dependent_task_id',
        'responsible_id',
        'team_id',
        'order',
        'estimated_hours',
        'actual_hours',
    ];

    protected function casts(): array
    {
        return [
            'expected_start_at' => 'datetime',
            'expected_end_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'estimated_hours' => 'decimal:2',
            'actual_hours' => 'decimal:2',
        ];
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function dependentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'dependent_task_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function taskTimes(): HasMany
    {
        return $this->hasMany(TaskTime::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable')->orderBy('created_at', 'desc');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable')->orderBy('created_at', 'desc');
    }

    public function activeTimes(): HasMany
    {
        return $this->hasMany(TaskTime::class)->whereNull('ended_at');
    }

    public function activeTimeForUser($userId): ?TaskTime
    {
        return $this->activeTimes()->where('user_id', $userId)->first();
    }

    public function getActiveOrPausedTimeForUser($userId): ?TaskTime
    {
        return $this->taskTimes()
            ->where('user_id', $userId)
            ->whereNull('ended_at')
            ->first();
    }

    public function canStart(): bool
    {
        if ($this->status !== 'planned') {
            return false;
        }

        if ($this->dependent_task_id && $this->dependentTask?->status !== 'completed') {
            return false;
        }

        return true;
    }

    public function startForUser($userId, $computerId = null): TaskTime
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => $this->started_at ?? now(),
        ]);

        return $this->taskTimes()->create([
            'user_id' => $userId,
            'started_at' => now(),
            'computer_id' => $computerId ?? request()->ip(),
        ]);
    }

    public function revertStart($userId): bool
    {
        // Só permite reverter se a tarefa está em progresso
        if ($this->status !== 'in_progress') {
            return false;
        }

        $activeTime = $this->activeTimeForUser($userId);

        if (!$activeTime) {
            return false;
        }

        // Delete o TaskTime do usuário (remove todo o tempo gasto)
        $activeTime->delete();

        // Recalcula as horas totais após deletar o tempo
        $this->refresh();
        $remainingTimes = $this->taskTimes()->count();

        // Se não houver mais tempos registrados, volta para planned
        if ($remainingTimes === 0) {
            $this->update([
                'status' => 'planned',
                'started_at' => null,
                'actual_hours' => 0,
            ]);
        } else {
            // Se ainda há outros usuários trabalhando, apenas recalcula as horas
            $actualHours = $this->calculateActualHours();
            $this->update([
                'actual_hours' => $actualHours,
            ]);
        }

        return true;
    }

    public function pauseForUser($userId): bool
    {
        $activeTime = $this->activeTimeForUser($userId);

        if (!$activeTime) {
            return false;
        }

        $activeTime->pause();

        // Recarrega para verificar se ainda há tempos ativos
        $this->refresh();
        if ($this->activeTimes()->whereNull('paused_at')->count() === 0) {
            $this->update(['status' => 'paused']);
        }

        return true;
    }

    public function resumeForUser($userId, $computerId = null): ?TaskTime
    {
        $existingTime = $this->taskTimes()
            ->where('user_id', $userId)
            ->whereNotNull('paused_at')
            ->whereNull('ended_at')
            ->first();

        if ($existingTime) {
            $existingTime->resume();
            $this->update(['status' => 'in_progress']);
            return $existingTime;
        }

        return $this->startForUser($userId, $computerId);
    }

    public function complete(): bool
    {
        if (!in_array($this->status, ['in_progress', 'paused'])) {
            return false;
        }

        $actualHours = $this->calculateActualHours();

        $this->update([
            'status' => 'completed',
            'ended_at' => now(),
            'actual_hours' => $actualHours,
        ]);

        return true;
    }

    private function calculateActualHours(): float
    {
        if (!$this->started_at) {
            return 0;
        }

        $endTime = now();
        $diffInHours = $this->started_at->diffInHours($endTime);

        return round($diffInHours, 2);
    }

    /**
     * Get total work time in minutes across all time logs
     */
    public function getTotalWorkMinutes(): int
    {
        return $this->taskTimes->sum(function ($timeLog) {
            return $timeLog->getTotalWorkMinutes();
        });
    }

    /**
     * Get total pause time in minutes across all time logs
     */
    public function getTotalPauseMinutes(): int
    {
        return $this->taskTimes->sum(function ($timeLog) {
            return $timeLog->getTotalPauseMinutes();
        });
    }

    /**
     * Get total elapsed time (work + pause) in minutes
     */
    public function getTotalElapsedMinutes(): int
    {
        return $this->getTotalWorkMinutes() + $this->getTotalPauseMinutes();
    }

    /**
     * Get formatted work time
     */
    public function getFormattedWorkTime(): string
    {
        return TaskTime::formatMinutes($this->getTotalWorkMinutes());
    }

    /**
     * Get formatted pause time
     */
    public function getFormattedPauseTime(): string
    {
        return TaskTime::formatMinutes($this->getTotalPauseMinutes());
    }

    /**
     * Get formatted elapsed time
     */
    public function getFormattedElapsedTime(): string
    {
        return TaskTime::formatMinutes($this->getTotalElapsedMinutes());
    }

    /**
     * Get work time for a specific user in minutes
     */
    public function getWorkMinutesForUser($userId): int
    {
        return $this->taskTimes()
            ->where('user_id', $userId)
            ->get()
            ->sum(function ($timeLog) {
                return $timeLog->getTotalWorkMinutes();
            });
    }

    /**
     * Get pause time for a specific user in minutes
     */
    public function getPauseMinutesForUser($userId): int
    {
        return $this->taskTimes()
            ->where('user_id', $userId)
            ->get()
            ->sum(function ($timeLog) {
                return $timeLog->getTotalPauseMinutes();
            });
    }

    /**
     * Get media summary (counts by type)
     */
    public function getMediaSummary(): array
    {
        $attachments = $this->attachments;

        $videos = $attachments->filter(fn($a) => str_starts_with($a->mime_type, 'video/'))->count();
        $images = $attachments->filter(fn($a) => str_starts_with($a->mime_type, 'image/'))->count();
        $documents = $attachments->filter(fn($a) =>
            !str_starts_with($a->mime_type, 'video/') &&
            !str_starts_with($a->mime_type, 'image/')
        )->count();

        return [
            'videos' => $videos,
            'images' => $images,
            'documents' => $documents,
            'total' => $attachments->count(),
        ];
    }

    /**
     * Get first video attachment
     */
    public function getFirstVideo(): ?Attachment
    {
        return $this->attachments()
            ->where('mime_type', 'like', 'video/%')
            ->first();
    }

    /**
     * Check if task has videos
     */
    public function hasVideos(): bool
    {
        return $this->attachments()
            ->where('mime_type', 'like', 'video/%')
            ->exists();
    }

    /**
     * Check if task has images
     */
    public function hasImages(): bool
    {
        return $this->attachments()
            ->where('mime_type', 'like', 'image/%')
            ->exists();
    }

    /**
     * Check if task has documents (non-video, non-image)
     */
    public function hasDocuments(): bool
    {
        return $this->attachments()
            ->where('mime_type', 'not like', 'video/%')
            ->where('mime_type', 'not like', 'image/%')
            ->exists();
    }
}