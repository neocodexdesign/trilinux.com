<?php

namespace App\Livewire\Projects;

use App\Models\Note;
use App\Models\Project;
use App\Models\Stage;
use App\Models\Task;
use App\Services\MarkdownNoteExporter;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectMarkdownPlan extends Component
{
    public Project $project;
    public Note $note;

    public string $markdown = '';
    public string $generated = '';

    public function mount(Project $project, MarkdownNoteExporter $exporter): void
    {
        $this->project = $project;
        $this->note = $this->loadOrCreatePlanNote();
        $this->markdown = $this->note->content ?? '';
        $this->generated = $exporter->generateChecklist($this->project, $this->note);
    }

    public function save(MarkdownNoteExporter $exporter): void
    {
        $this->note->update([
            'content' => $this->markdown,
            'is_pinned' => true,
        ]);

        $this->generated = $exporter->generateChecklist($this->project, $this->note);

        $this->dispatch('toast-show',
            duration: 2500,
            slots: ['text' => 'Markdown salvo.'],
            dataset: ['variant' => 'success'],
        );
    }

    public function export(MarkdownNoteExporter $exporter): void
    {
        $this->note->update([
            'content' => $this->markdown,
            'is_pinned' => true,
        ]);

        $exporter->export($this->project, $this->note);
        $this->generated = $exporter->generateChecklist($this->project, $this->note);

        $this->dispatch('toast-show',
            duration: 3000,
            slots: ['text' => 'Exportado para o projeto.'],
            dataset: ['variant' => 'success'],
        );
    }

    public function toggle(int $taskId, MarkdownNoteExporter $exporter): void
    {
        $task = Task::query()
            ->where('id', $taskId)
            ->where('source_note_id', $this->note->id)
            ->where('source', MarkdownNoteExporter::SOURCE)
            ->whereHas('stage', function ($q) {
                $q->where('project_id', $this->project->id)
                    ->where('source_note_id', $this->note->id)
                    ->where('source', MarkdownNoteExporter::SOURCE);
            })
            ->firstOrFail();

        $exporter->toggleTask($task);
        $this->generated = $exporter->generateChecklist($this->project, $this->note);
    }

    public function refreshGenerated(MarkdownNoteExporter $exporter): void
    {
        $this->generated = $exporter->generateChecklist($this->project, $this->note);
    }

    public function render()
    {
        $stages = Stage::query()
            ->where('project_id', $this->project->id)
            ->where('source_note_id', $this->note->id)
            ->where('source', MarkdownNoteExporter::SOURCE)
            ->orderBy('order')
            ->with(['tasks' => function ($query) {
                $query
                    ->where('source_note_id', $this->note->id)
                    ->where('source', MarkdownNoteExporter::SOURCE)
                    ->orderBy('order');
            }])
            ->get();

        return view('livewire.projects.project-markdown-plan', [
            'stages' => $stages,
        ])->layoutData([
            'title' => 'Markdown Plan',
        ]);
    }

    private function loadOrCreatePlanNote(): Note
    {
        $existing = $this->project
            ->notes()
            ->where('is_pinned', true)
            ->where('content', 'like', '# %')
            ->orderByDesc('updated_at')
            ->first();

        if ($existing) {
            return $existing;
        }

        return $this->project->notes()->create([
            'user_id' => Auth::id(),
            'content' => $this->defaultTemplate(),
            'is_pinned' => true,
        ]);
    }

    private function defaultTemplate(): string
    {
        return "# Plano do Projeto: {$this->project->name}\n\n## Geral\n- [ ] Defina as etapas\n- [ ] Liste as tarefas\n";
    }
}

