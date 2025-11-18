<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notable_type',
        'notable_id',
        'content',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the parent notable model (Task, Stage, or Project)
     */
    public function notable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who created the note
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all attachments for the note
     */
    public function attachments()
    {
        return $this->hasMany(NoteAttachment::class);
    }

    /**
     * Scope to get only pinned notes
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Toggle pin status
     */
    public function togglePin(): void
    {
        $this->is_pinned = !$this->is_pinned;
        $this->save();
    }
}
