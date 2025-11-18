<?php

namespace App\Livewire\Tasks;

use Livewire\Component;

class MyCompletedTasks extends Component
{
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

        return view('livewire.tasks.my-completed-tasks', [
            'groupedTasks' => $groupedTasks,
            'totalTasks' => $totalTasks,
        ]);
    }
}
