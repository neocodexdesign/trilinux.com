<?php

namespace App\Livewire\Dashboard;

use App\Models\Attachment;
use App\Models\Task;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaskAttachments extends Component
{
    use WithFileUploads;

    public bool $showModal = false;
    public ?int $taskId = null;
    public ?string $taskName = null;
    public $files = [];
    public string $description = '';
    public ?int $editingAttachmentId = null;
    public string $editingDescription = '';

    protected $rules = [
        'files.*' => 'file|max:51200', // 50MB max per file
        'description' => 'nullable|string|max:500',
    ];

    #[On('open-task-attachments')]
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

    public function uploadFiles()
    {
        $this->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|max:51200',
        ]);

        $task = Task::find($this->taskId);

        if (!$task) {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            return;
        }

        foreach ($this->files as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();

            // Generate unique filename
            $storedFilename = Str::uuid() . '.' . $extension;

            // Store file in public disk using Storage facade
            $path = Storage::disk('public')->putFileAs('attachments', $file, $storedFilename);

            if (!$path) {
                Flux::toast('Erro ao salvar arquivo: ' . $originalName, variant: 'danger');
                continue;
            }

            // Create attachment record
            $task->attachments()->create([
                'user_id' => auth()->id(),
                'filename' => $originalName,
                'stored_filename' => $storedFilename,
                'mime_type' => $mimeType,
                'size' => $size,
                'extension' => $extension,
                'description' => $this->description ?: null,
            ]);
        }

        $this->files = [];
        $this->description = '';
        Flux::toast('Arquivo(s) enviado(s) com sucesso', variant: 'success');
    }

    public function startEditing($attachmentId)
    {
        $attachment = Attachment::find($attachmentId);

        if (!$attachment) {
            Flux::toast('Arquivo não encontrado', variant: 'danger');
            return;
        }

        $this->editingAttachmentId = $attachmentId;
        $this->editingDescription = $attachment->description ?? '';
    }

    public function cancelEditing()
    {
        $this->editingAttachmentId = null;
        $this->editingDescription = '';
    }

    public function updateAttachment()
    {
        $attachment = Attachment::find($this->editingAttachmentId);

        if (!$attachment) {
            Flux::toast('Arquivo não encontrado', variant: 'danger');
            return;
        }

        if ($attachment->user_id !== auth()->id()) {
            Flux::toast('Você não tem permissão para editar este arquivo', variant: 'danger');
            return;
        }

        $attachment->update([
            'description' => $this->editingDescription ?: null,
        ]);

        $this->cancelEditing();
        Flux::toast('Descrição atualizada com sucesso', variant: 'success');
    }

    public function deleteAttachment($attachmentId)
    {
        $attachment = Attachment::find($attachmentId);

        if (!$attachment) {
            Flux::toast('Arquivo não encontrado', variant: 'danger');
            return;
        }

        if ($attachment->user_id !== auth()->id()) {
            Flux::toast('Você não tem permissão para excluir este arquivo', variant: 'danger');
            return;
        }

        $attachment->delete();
        Flux::toast('Arquivo excluído com sucesso', variant: 'success');
    }

    public function downloadAttachment($attachmentId)
    {
        $attachment = Attachment::find($attachmentId);

        if (!$attachment) {
            Flux::toast('Arquivo não encontrado', variant: 'danger');
            return;
        }

        $filePath = storage_path('app/public/attachments/' . $attachment->stored_filename);

        if (!file_exists($filePath)) {
            Flux::toast('Arquivo não encontrado no servidor', variant: 'danger');
            return;
        }

        return response()->download($filePath, $attachment->filename);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->taskId = null;
        $this->taskName = null;
        $this->files = [];
        $this->description = '';
        $this->cancelEditing();
    }

    public function getFileIcon($mimeType, $extension)
    {
        // Images
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        // Videos
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        // Audio
        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        // PDF
        if ($mimeType === 'application/pdf' || $extension === 'pdf') {
            return 'pdf';
        }

        // Documents
        if (in_array($extension, ['doc', 'docx', 'odt', 'rtf'])) {
            return 'document';
        }

        // Spreadsheets
        if (in_array($extension, ['xls', 'xlsx', 'ods', 'csv'])) {
            return 'spreadsheet';
        }

        // Archives
        if (in_array($extension, ['zip', 'rar', '7z', 'tar', 'gz'])) {
            return 'archive';
        }

        // Code
        if (in_array($extension, ['php', 'js', 'ts', 'html', 'css', 'json', 'xml', 'py', 'rb'])) {
            return 'code';
        }

        return 'file';
    }

    public function render()
    {
        $attachments = collect();

        if ($this->taskId) {
            $attachments = Attachment::where('attachable_type', Task::class)
                ->where('attachable_id', $this->taskId)
                ->with('user')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('livewire.dashboard.task-attachments', [
            'attachments' => $attachments,
        ]);
    }
}
