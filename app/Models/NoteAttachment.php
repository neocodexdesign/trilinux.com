<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_id',
        'filename',
        'path',
        'mime_type',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the note that owns the attachment
     */
    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    /**
     * Get file icon based on mime type
     */
    public function getIconAttribute(): string
    {
        if (str_starts_with($this->mime_type, 'image/')) {
            return '🖼️';
        }
        if (str_starts_with($this->mime_type, 'video/')) {
            return '🎥';
        }
        if (str_starts_with($this->mime_type, 'audio/')) {
            return '🎵';
        }
        if ($this->mime_type === 'application/pdf') {
            return '📄';
        }
        if (in_array($this->mime_type, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return '📝';
        }
        if (in_array($this->mime_type, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
            return '📊';
        }
        if (str_contains($this->mime_type, 'zip') || str_contains($this->mime_type, 'compressed')) {
            return '📦';
        }
        return '📎';
    }

    /**
     * Get human readable file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}
