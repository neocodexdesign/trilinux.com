<?php

namespace App\Services;

use App\Models\Note;
use App\Models\Project;
use App\Models\Stage;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarkdownNoteExporter
{
    public const SOURCE = 'markdown_note';

    /**
     * @return array<int, array{name: string, tasks: array<int, array{name: string, checked: bool}>}>
     */
    public function parse(string $markdown): array
    {
        $lines = preg_split("/\\r\\n|\\n|\\r/", $markdown) ?: [];

        $stages = [];
        $currentStageIndex = null;

        foreach ($lines as $line) {
            if (preg_match('/^\\s*##\\s+(.+)\\s*$/u', $line, $m)) {
                $name = trim($m[1]);
                if ($name === '') {
                    continue;
                }

                $stages[] = [
                    'name' => $name,
                    'tasks' => [],
                ];
                $currentStageIndex = count($stages) - 1;
                continue;
            }

            if (preg_match('/^\\s*-\\s*\\[( |x|X)\\]\\s+(.+)\\s*$/u', $line, $m)) {
                $checked = strtolower($m[1]) === 'x';
                $taskName = trim($m[2]);
                if ($taskName === '') {
                    continue;
                }

                if ($currentStageIndex === null) {
                    $stages[] = [
                        'name' => 'Geral',
                        'tasks' => [],
                    ];
                    $currentStageIndex = count($stages) - 1;
                }

                $stages[$currentStageIndex]['tasks'][] = [
                    'name' => $taskName,
                    'checked' => $checked,
                ];
            }
        }

        return $stages;
    }

    public function export(Project $project, Note $note): void
    {
        DB::transaction(function () use ($project, $note) {
            $parsedStages = $this->parse($note->content ?? '');

            $stageOrder = 1;

            foreach ($parsedStages as $parsedStage) {
                $stageKey = $this->stableKey($parsedStage['name']);

                $stage = Stage::firstOrNew([
                    'project_id' => $project->id,
                    'source_note_id' => $note->id,
                    'source' => self::SOURCE,
                    'source_key' => $stageKey,
                ]);

                $stage->fill([
                    'tenant_id' => $project->tenant_id,
                    'team_id' => $project->team_id,
                    'responsible_id' => $project->responsible_id,
                    'name' => $parsedStage['name'],
                    'order' => $stageOrder,
                    'source_note_id' => $note->id,
                    'source' => self::SOURCE,
                    'source_key' => $stageKey,
                ]);

                $stage->save();

                $taskOrder = 1;
                foreach ($parsedStage['tasks'] as $parsedTask) {
                    $taskKey = $this->stableKey($parsedTask['name']);

                    $task = Task::firstOrNew([
                        'stage_id' => $stage->id,
                        'source_note_id' => $note->id,
                        'source' => self::SOURCE,
                        'source_key' => $taskKey,
                    ]);

                    $task->fill([
                        'tenant_id' => $project->tenant_id,
                        'team_id' => $project->team_id,
                        'responsible_id' => $project->responsible_id,
                        'name' => $parsedTask['name'],
                        'order' => $taskOrder,
                        'source_note_id' => $note->id,
                        'source' => self::SOURCE,
                        'source_key' => $taskKey,
                    ]);

                    if ($parsedTask['checked']) {
                        $task->status = 'completed';
                        $task->started_at = $task->started_at ?? now();
                        $task->ended_at = now();
                    } else {
                        if ($task->status === 'completed') {
                            $task->status = $task->started_at ? 'in_progress' : 'planned';
                        } elseif (!$task->exists) {
                            $task->status = 'planned';
                        }
                        $task->ended_at = null;
                    }

                    $task->save();
                    $taskOrder++;
                }

                $this->recalcStageStatus($stage);
                $stageOrder++;
            }
        });
    }

    public function toggleTask(Task $task): void
    {
        DB::transaction(function () use ($task) {
            $task->refresh();

            if ($task->status === 'completed') {
                $task->update([
                    'status' => $task->started_at ? 'in_progress' : 'planned',
                    'ended_at' => null,
                ]);
            } else {
                $task->update([
                    'status' => 'completed',
                    'started_at' => $task->started_at ?? now(),
                    'ended_at' => now(),
                ]);
            }

            $task->refresh();
            $this->recalcStageStatus($task->stage);
        });
    }

    public function recalcStageStatus(Stage $stage): void
    {
        $tasksQuery = Task::query()
            ->where('stage_id', $stage->id)
            ->where('source_note_id', $stage->source_note_id)
            ->where('source', self::SOURCE);

        $total = (clone $tasksQuery)->count();
        $completed = (clone $tasksQuery)->where('status', 'completed')->count();

        $newStatus = 'planned';
        $endedAt = null;
        $startedAt = $stage->started_at;

        if ($total > 0 && $completed === $total) {
            $newStatus = 'completed';
            $startedAt = $startedAt ?? now();
            $endedAt = $stage->ended_at ?? now();
        } elseif ($completed > 0) {
            $newStatus = 'in_progress';
            $startedAt = $startedAt ?? now();
            $endedAt = null;
        }

        $stage->update([
            'status' => $newStatus,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ]);
    }

    public function generateChecklist(Project $project, Note $note): string
    {
        $title = $this->extractTitle($note->content ?? '') ?? 'Checklist';

        $stages = Stage::query()
            ->where('project_id', $project->id)
            ->where('source_note_id', $note->id)
            ->where('source', self::SOURCE)
            ->orderBy('order')
            ->with(['tasks' => function ($query) use ($note) {
                $query
                    ->where('source_note_id', $note->id)
                    ->where('source', self::SOURCE)
                    ->orderBy('order');
            }])
            ->get();

        $out = [];
        $out[] = '# ' . $title;
        $out[] = '';

        foreach ($stages as $stage) {
            $out[] = '## ' . $stage->name;
            foreach ($stage->tasks as $task) {
                $box = $task->status === 'completed' ? 'x' : ' ';
                $out[] = '- [' . $box . '] ' . $task->name;
            }
            $out[] = '';
        }

        return rtrim(implode("\n", $out)) . "\n";
    }

    private function extractTitle(string $markdown): ?string
    {
        $lines = preg_split("/\\r\\n|\\n|\\r/", $markdown) ?: [];

        foreach ($lines as $line) {
            if (preg_match('/^\\s*#\\s+(.+)\\s*$/u', $line, $m)) {
                return trim($m[1]) ?: null;
            }
        }

        return null;
    }

    private function stableKey(string $value): string
    {
        $normalized = Str::of($value)
            ->trim()
            ->lower()
            ->replaceMatches('/\\s+/u', ' ')
            ->toString();

        return hash('sha1', $normalized);
    }
}

