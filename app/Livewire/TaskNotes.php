<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Note;
use App\Models\NoteAttachment;
use Illuminate\Support\Facades\Storage;

class TaskNotes extends Component
{
    use WithFileUploads;

    public $task;
    public $showModal = false;
    public $newNoteContent = '';
    public $editingNoteId = null;
    public $editingContent = '';
    public $attachments = [];

    protected $rules = [
        'newNoteContent' => 'required|string|min:1',
        'editingContent' => 'required|string|min:1',
        'attachments.*' => 'file|max:10240', // Max 10MB per file
    ];

    public function mount($task)
    {
        $this->task = $task;
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->newNoteContent = '';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->newNoteContent = '';
        $this->editingNoteId = null;
        $this->editingContent = '';
        $this->attachments = [];
        $this->resetValidation();
    }

    public function createNote()
    {
        $this->validate(['newNoteContent' => 'required|string|min:1']);

        $note = $this->task->notes()->create([
            'user_id' => auth()->id(),
            'content' => $this->newNoteContent,
            'is_pinned' => false,
        ]);

        // Upload attachments if any
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
                $path = $file->store('note-attachments', 'public');

                $note->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        $this->newNoteContent = '';
        $this->attachments = [];
        $this->dispatch('note-created');
    }

    public function editNote($noteId)
    {
        $note = $this->task->notes()->find($noteId);

        if ($note && $note->user_id === auth()->id()) {
            $this->editingNoteId = $noteId;
            $this->editingContent = $note->content;
        }
    }

    public function updateNote()
    {
        $this->validate(['editingContent' => 'required|string|min:1']);

        $note = $this->task->notes()->find($this->editingNoteId);

        if ($note && $note->user_id === auth()->id()) {
            $note->update(['content' => $this->editingContent]);
            $this->dispatch('note-updated');
        }

        $this->editingNoteId = null;
        $this->editingContent = '';
    }

    public function cancelEdit()
    {
        $this->editingNoteId = null;
        $this->editingContent = '';
    }

    public function deleteNote($noteId)
    {
        $note = $this->task->notes()->find($noteId);

        if ($note && $note->user_id === auth()->id()) {
            $note->delete();
            $this->dispatch('note-deleted');
        }
    }

    public function togglePin($noteId)
    {
        $note = $this->task->notes()->find($noteId);

        if ($note && $note->user_id === auth()->id()) {
            $note->togglePin();
            $this->dispatch('note-pinned');
        }
    }

    public function deleteAttachment($attachmentId)
    {
        $attachment = NoteAttachment::find($attachmentId);

        if ($attachment && $attachment->note->user_id === auth()->id()) {
            // Delete file from storage
            Storage::disk('public')->delete($attachment->path);
            // Delete record
            $attachment->delete();
            $this->dispatch('attachment-deleted');
        }
    }

    public function render()
    {
        $notes = $this->task->notes()
            ->with(['user', 'attachments'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.task-notes', [
            'notes' => $notes,
            'notesCount' => $notes->count(),
        ]);
    }
}
