<?php

namespace App\Livewire;

use App\Models\Attachment;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentManager extends Component
{
    use WithFileUploads;

    public $attachable; // The model (Task, Stage, or Project)
    public $upload;
    public $description = '';
    public $editingAttachmentId = null;
    public $editingDescription = '';

    // Blocked extensions for security
    protected $blockedExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js'];

    protected $rules = [
        'upload' => 'required|file|max:51200', // 50MB max
        'description' => 'nullable|string|max:500',
    ];

    public function mount($attachable)
    {
        $this->attachable = $attachable;
    }

    public function uploadFile()
    {
        $this->validate();

        if (!$this->upload) {
            $this->dispatch('error', message: 'Please select a file');
            return;
        }

        $extension = strtolower($this->upload->getClientOriginalExtension());

        // Security check: block dangerous files
        if (in_array($extension, $this->blockedExtensions)) {
            $this->dispatch('error', message: 'This file type is not allowed for security reasons');
            return;
        }

        $originalName = $this->upload->getClientOriginalName();
        $storedName = Str::uuid() . '.' . $extension;

        // Store file
        $this->upload->storeAs('attachments', $storedName);

        // Create attachment record
        $this->attachable->attachments()->create([
            'user_id' => auth()->id(),
            'filename' => $originalName,
            'stored_filename' => $storedName,
            'mime_type' => $this->upload->getMimeType(),
            'size' => $this->upload->getSize(),
            'extension' => $extension,
            'description' => $this->description,
        ]);

        $this->reset(['upload', 'description']);
        $this->dispatch('attachment-uploaded');
    }

    public function editAttachment($attachmentId)
    {
        $attachment = $this->attachable->attachments()->find($attachmentId);

        if ($attachment && $attachment->user_id === auth()->id()) {
            $this->editingAttachmentId = $attachmentId;
            $this->editingDescription = $attachment->description ?? '';
        }
    }

    public function updateAttachment()
    {
        $attachment = $this->attachable->attachments()->find($this->editingAttachmentId);

        if ($attachment && $attachment->user_id === auth()->id()) {
            $attachment->update(['description' => $this->editingDescription]);
            $this->dispatch('attachment-updated');
        }

        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->editingAttachmentId = null;
        $this->editingDescription = '';
    }

    public function deleteAttachment($attachmentId)
    {
        $attachment = $this->attachable->attachments()->find($attachmentId);

        if ($attachment && $attachment->user_id === auth()->id()) {
            $attachment->delete();
            $this->dispatch('attachment-deleted');
        }
    }

    public function downloadAttachment($attachmentId)
    {
        $attachment = $this->attachable->attachments()->find($attachmentId);

        if ($attachment) {
            return Storage::download('attachments/' . $attachment->stored_filename, $attachment->filename);
        }
    }

    public function render()
    {
        $attachments = $this->attachable->attachments()->with('user')->get();

        return view('livewire.attachment-manager', [
            'attachments' => $attachments,
        ]);
    }
}
