<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'started_at',
        'ended_at',
        'paused_at',
        'resumed_at',
        'work_minutes',
        'pause_minutes',
        'computer_id',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'paused_at' => 'datetime',
            'resumed_at' => 'datetime',
            'work_minutes' => 'integer',
            'pause_minutes' => 'integer',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this time log is currently active (not paused, not ended)
     */
    public function isActive(): bool
    {
        return is_null($this->paused_at) && is_null($this->ended_at);
    }

    /**
     * Check if this time log is paused
     */
    public function isPaused(): bool
    {
        return !is_null($this->paused_at) && is_null($this->ended_at);
    }

    /**
     * Pause this time tracking session
     */
    public function pause(): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->paused_at = now();

        // Calculate work minutes since start or last resume
        $startTime = $this->resumed_at ?? $this->started_at;
        $workMinutes = $startTime->diffInMinutes($this->paused_at);
        $this->work_minutes = ($this->work_minutes ?? 0) + $workMinutes;

        $this->save();
    }

    /**
     * Resume this time tracking session
     */
    public function resume(): void
    {
        if (!$this->isPaused()) {
            return;
        }

        $this->resumed_at = now();

        // Calculate pause minutes
        $pauseMinutes = $this->paused_at->diffInMinutes($this->resumed_at);
        $this->pause_minutes = ($this->pause_minutes ?? 0) + $pauseMinutes;

        // Clear paused_at to mark as active again
        $this->paused_at = null;

        $this->save();
    }

    /**
     * End this time tracking session
     */
    public function end(): void
    {
        if (!is_null($this->ended_at)) {
            return;
        }

        $this->ended_at = now();

        // If still active (not paused), calculate final work time
        if ($this->isActive()) {
            $startTime = $this->resumed_at ?? $this->started_at;
            $workMinutes = $startTime->diffInMinutes($this->ended_at);
            $this->work_minutes = ($this->work_minutes ?? 0) + $workMinutes;
        }

        $this->save();
    }

    /**
     * Get total work time in minutes
     */
    public function getTotalWorkMinutes(): int
    {
        $total = $this->work_minutes ?? 0;

        // If currently active, add ongoing time
        if ($this->isActive()) {
            $startTime = $this->resumed_at ?? $this->started_at;
            $total += $startTime->diffInMinutes(now());
        }

        return $total;
    }

    /**
     * Get total pause time in minutes
     */
    public function getTotalPauseMinutes(): int
    {
        $total = $this->pause_minutes ?? 0;

        // If currently paused, add ongoing pause time
        if ($this->isPaused()) {
            $total += $this->paused_at->diffInMinutes(now());
        }

        return $total;
    }

    /**
     * Get total elapsed time (work + pause) in minutes
     */
    public function getTotalElapsedMinutes(): int
    {
        return $this->getTotalWorkMinutes() + $this->getTotalPauseMinutes();
    }

    /**
     * Format minutes to human readable time (e.g., "2h 30m")
     */
    public static function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes}m";
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($mins === 0) {
            return "{$hours}h";
        }

        return "{$hours}h {$mins}m";
    }

    /**
     * Get formatted work time
     */
    public function getFormattedWorkTime(): string
    {
        return self::formatMinutes($this->getTotalWorkMinutes());
    }

    /**
     * Get formatted pause time
     */
    public function getFormattedPauseTime(): string
    {
        return self::formatMinutes($this->getTotalPauseMinutes());
    }

    /**
     * Get formatted elapsed time
     */
    public function getFormattedElapsedTime(): string
    {
        return self::formatMinutes($this->getTotalElapsedMinutes());
    }
}
