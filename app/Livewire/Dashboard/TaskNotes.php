<?php

namespace App\Livewire\Dashboard;

use App\Models\Note;
use App\Models\Task;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;

class TaskNotes extends Component
{
    public bool $showModal = false;
    public bool $showCreateModal = false;
    public ?int $taskId = null;
    public ?string $taskName = null;
    public string $newNoteContent = '';
    public ?int $editingNoteId = null;
    public string $editingNoteContent = '';

    #[On('open-task-notes')]
    public function openModal($taskId)
    {
        $task = Task::find($taskId);

        if (!$task) {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            return;
        }

        $this->taskId = $taskId;
        $this->taskName = $task->name;
        $this->showModal = true;
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->newNoteContent = '';
    }

    public function createNote()
    {
        if (empty(trim($this->newNoteContent))) {
            Flux::toast('O conteúdo da nota é obrigatório', variant: 'danger');
            return;
        }

        $task = Task::find($this->taskId);

        if (!$task) {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            return;
        }

        $task->notes()->create([
            'user_id' => auth()->id(),
            'content' => $this->newNoteContent,
            'is_pinned' => false,
        ]);

        $this->newNoteContent = '';
        $this->showCreateModal = false;
        Flux::toast('Nota criada com sucesso', variant: 'success');
    }

    public function startEditing($noteId)
    {
        $note = Note::find($noteId);

        if (!$note) {
            Flux::toast('Nota não encontrada', variant: 'danger');
            return;
        }

        $this->editingNoteId = $noteId;
        $this->editingNoteContent = $note->content;
    }

    public function cancelEditing()
    {
        $this->editingNoteId = null;
        $this->editingNoteContent = '';
    }

    public function updateNote()
    {
        if (empty(trim($this->editingNoteContent))) {
            Flux::toast('O conteúdo da nota é obrigatório', variant: 'danger');
            return;
        }

        $note = Note::find($this->editingNoteId);

        if (!$note) {
            Flux::toast('Nota não encontrada', variant: 'danger');
            return;
        }

        // Verificar se o usuário tem permissão para editar
        if ($note->user_id !== auth()->id()) {
            Flux::toast('Você não tem permissão para editar esta nota', variant: 'danger');
            return;
        }

        $note->update([
            'content' => $this->editingNoteContent,
        ]);

        $this->cancelEditing();
        Flux::toast('Nota atualizada com sucesso', variant: 'success');
    }

    public function deleteNote($noteId)
    {
        $note = Note::find($noteId);

        if (!$note) {
            Flux::toast('Nota não encontrada', variant: 'danger');
            return;
        }

        // Verificar se o usuário tem permissão para deletar
        if ($note->user_id !== auth()->id()) {
            Flux::toast('Você não tem permissão para excluir esta nota', variant: 'danger');
            return;
        }

        $note->delete();
        Flux::toast('Nota excluída com sucesso', variant: 'success');
    }

    public function togglePin($noteId)
    {
        $note = Note::find($noteId);

        if (!$note) {
            Flux::toast('Nota não encontrada', variant: 'danger');
            return;
        }

        $note->togglePin();
        Flux::toast($note->is_pinned ? 'Nota fixada' : 'Nota desafixada', variant: 'success');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showCreateModal = false;
        $this->taskId = null;
        $this->taskName = null;
        $this->newNoteContent = '';
        $this->cancelEditing();
    }

    public function render()
    {
        $notes = collect();

        if ($this->taskId) {
            $notes = Note::where('notable_type', Task::class)
                ->where('notable_id', $this->taskId)
                ->with('user')
                ->orderByDesc('is_pinned')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('livewire.dashboard.task-notes', [
            'notes' => $notes,
        ]);
    }
}
