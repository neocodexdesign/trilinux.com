<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attachable_type',
        'attachable_id',
        'filename',
        'stored_filename',
        'mime_type',
        'size',
        'extension',
        'description',
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
     * Get the parent attachable model (Project, Stage, or Task)
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded the attachment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full file path
     */
    public function getFilePath(): string
    {
        return storage_path('app/public/attachments/' . $this->stored_filename);
    }

    /**
     * Get the public URL for the file (via Laravel route to avoid redirect issues)
     */
    public function getPublicUrl(): string
    {
        return route('attachments.view', $this->id);
    }

    /**
     * Get the download URL
     */
    public function getDownloadUrl(): string
    {
        return route('attachments.download', $this->id);
    }

    /**
     * Get human-readable file size
     */
    public function getFormattedSize(): string
    {
        $bytes = $this->size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 2) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }

    /**
     * Delete file when model is deleted
     */
    protected static function booted(): void
    {
        static::deleting(function (Attachment $attachment) {
            Storage::disk('public')->delete('attachments/' . $attachment->stored_filename);
        });
    }
}
