<?php

namespace App\Livewire\MarkdownPlans;

use App\Models\Note;
use App\Models\Project;
use App\Models\Stage;
use App\Models\Task;
use App\Services\MarkdownNoteExporter;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MarkdownPlansTable extends Component
{
    use WithPagination;

    public function reintegrate(int $noteId, MarkdownNoteExporter $exporter): void
    {
        [$project, $note] = $this->resolveProjectAndNote($noteId);

        $exporter->export($project, $note);

        $this->dispatch('toast-show',
            duration: 3000,
            slots: ['text' => 'Plano reintegrado no projeto.'],
            dataset: ['variant' => 'success'],
        );
    }

    public function rebuildChecklist(int $noteId, MarkdownNoteExporter $exporter): void
    {
        [$project, $note] = $this->resolveProjectAndNote($noteId);

        $exporter->recalcAllStages($project, $note);
        $exporter->syncMarkdownFromSystem($project, $note);

        $this->dispatch('toast-show',
            duration: 3000,
            slots: ['text' => 'Checklist refeito e markdown atualizado.'],
            dataset: ['variant' => 'success'],
        );
    }

    public function purgeGenerated(int $noteId, MarkdownNoteExporter $exporter): void
    {
        [$project, $note] = $this->resolveProjectAndNote($noteId);

        $exporter->purge($project, $note);

        $this->dispatch('toast-show',
            duration: 3000,
            slots: ['text' => 'Stages e tasks gerados foram excluídos.'],
            dataset: ['variant' => 'success'],
        );
    }

    public function deletePlan(int $noteId, MarkdownNoteExporter $exporter): void
    {
        [$project, $note] = $this->resolveProjectAndNote($noteId);

        DB::transaction(function () use ($project, $note, $exporter) {
            $exporter->purge($project, $note);
            $note->delete();
        });

        $this->dispatch('toast-show',
            duration: 3000,
            slots: ['text' => 'Plano excluído.'],
            dataset: ['variant' => 'success'],
        );
    }

    public function open(int $noteId)
    {
        [$project, $note] = $this->resolveProjectAndNote($noteId);

        return redirect()->route('projects.markdown-plan', [$project, $note]);
    }

    public function render()
    {
        $plans = Note::query()
            ->where('notable_type', Project::class)
            ->where('is_pinned', true)
            ->where(function ($q) {
                $q->where('content', 'like', MarkdownNoteExporter::NOTE_MARKER . '%')
                    ->orWhereExists(function ($sub) {
                        $sub->selectRaw('1')
                            ->from('stages')
                            ->whereColumn('stages.source_note_id', 'notes.id')
                            ->where('stages.source', MarkdownNoteExporter::SOURCE);
                    });
            })
            ->with(['notable' => function ($q) {
                $q->with(['responsible', 'team']);
            }, 'user'])
            ->orderByDesc('updated_at')
            ->paginate(10);

        $noteIds = $plans->getCollection()->pluck('id')->all();

        $stageCounts = Stage::query()
            ->selectRaw('source_note_id, count(*) as cnt')
            ->where('source', MarkdownNoteExporter::SOURCE)
            ->whereIn('source_note_id', $noteIds)
            ->groupBy('source_note_id')
            ->pluck('cnt', 'source_note_id');

        $taskCounts = Task::query()
            ->selectRaw('source_note_id, count(*) as cnt')
            ->where('source', MarkdownNoteExporter::SOURCE)
            ->whereIn('source_note_id', $noteIds)
            ->groupBy('source_note_id')
            ->pluck('cnt', 'source_note_id');

        return view('livewire.markdown-plans.markdown-plans-table', [
            'plans' => $plans,
            'stageCounts' => $stageCounts,
            'taskCounts' => $taskCounts,
        ]);
    }

    /**
     * @return array{0: Project, 1: Note}
     */
    private function resolveProjectAndNote(int $noteId): array
    {
        $note = Note::query()
            ->where('id', $noteId)
            ->where('notable_type', Project::class)
            ->firstOrFail();

        /** @var Project $project */
        $project = $note->notable()->firstOrFail();

        return [$project, $note];
    }
}
