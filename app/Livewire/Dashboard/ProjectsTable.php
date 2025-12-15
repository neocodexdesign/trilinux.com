<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectsTable extends Component
{
    use WithPagination;

    public function render()
    {
        $projects = Project::query()
            ->with(['responsible', 'team'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.dashboard.projects-table', [
            'projects' => $projects,
        ]);
    }
}

