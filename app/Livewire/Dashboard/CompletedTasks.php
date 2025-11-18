<?php

namespace App\Livewire\Dashboard;

use App\Models\Task;
use Livewire\Component;
use Livewire\WithPagination;

class CompletedTasks extends Component
{
    use WithPagination;

    public int $perPage = 5;

    public function render()
    {
        $tasks = auth()->user()
            ->assignedTasks()
            ->where('status', 'completed')
            ->with(['stage.project'])
            ->orderBy('ended_at', 'desc')
            ->get();

        // Group tasks by project and stage
        $groupedTasks = $tasks->groupBy(function ($task) {
            return $task->stage?->project?->id ?? 'no-project';
        })->map(function ($projectTasks) {
            return [
                'project' => $projectTasks->first()->stage?->project,
                'stages' => $projectTasks->groupBy(function ($task) {
                    return $task->stage?->id ?? 'no-stage';
                })->map(function ($stageTasks) {
                    return [
                        'stage' => $stageTasks->first()->stage,
                        'tasks' => $stageTasks,
                    ];
                })->values(),
            ];
        })->values();

        $totalTasks = $tasks->count();

        return view('livewire.dashboard.completed-tasks', [
            'groupedTasks' => $groupedTasks,
            'totalTasks' => $totalTasks,
        ]);
    }

    public function goToPage($page)
    {
        $this->setPage($page);
    }
}
