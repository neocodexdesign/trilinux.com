<?php

namespace App\Filament\Tenant\Resources\Tasks\Pages;

use App\Filament\Tenant\Resources\Tasks\TaskResource;
use App\Models\Project;
use App\Models\Stage;
use App\Models\Task;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Tabs\Tab;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        $totalTasks = Task::count();
        $tabs['all'] = Tab::make('Todas as Tarefas')
            ->badge($totalTasks);

        $projects = Project::with('stages')->orderBy('name')->get();

        foreach ($projects as $project) {
            $projectTasksCount = Task::whereHas('stage', function ($query) use ($project) {
                $query->where('project_id', $project->id);
            })->count();

            $tabs["project_{$project->id}"] = Tab::make($project->name)
                ->badge($projectTasksCount)
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereHas('stage', function (Builder $subQuery) use ($project) {
                        $subQuery->where('project_id', $project->id);
                    })
                );

            foreach ($project->stages as $stage) {
                $stageTasksCount = $stage->tasks()->count();

                $tabs["stage_{$stage->id}"] = Tab::make("└ {$stage->name}")
                    ->badge($stageTasksCount)
                    ->modifyQueryUsing(
                        fn(Builder $query) =>
                        $query->where('stage_id', $stage->id)
                    );
            }
        }

        return $tabs;
    }
}