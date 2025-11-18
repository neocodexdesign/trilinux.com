<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Stage;
use Livewire\Component;

class ProjectEditor extends Component
{
    public ?Project $project = null;
    public $projectId = null;
    public $activeTab = 'details';
    public $showModal = false;

    // Project fields
    public $name = '';
    public $description = '';
    public $status = 'planned';
    public $expected_start_at = '';
    public $expected_end_at = '';

    // New Stage
    public $newStageName = '';

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:planned,in_progress,paused,completed,cancelled',
        'expected_start_at' => 'nullable|date',
        'expected_end_at' => 'nullable|date|after_or_equal:expected_start_at',
    ];

    protected $listeners = ['openProjectEditor' => 'openModal'];

    public function mount($projectId = null)
    {
        if ($projectId) {
            $this->projectId = $projectId;
            $this->loadProject();
        }
    }

    public function loadProject()
    {
        $this->project = Project::with(['stages.tasks', 'notes', 'attachments'])
            ->findOrFail($this->projectId);

        $this->name = $this->project->name;
        $this->description = $this->project->description;
        $this->status = $this->project->status;
        $this->expected_start_at = $this->project->expected_start_at?->format('Y-m-d');
        $this->expected_end_at = $this->project->expected_end_at?->format('Y-m-d');
    }

    public function openModal($projectId = null)
    {
        $this->projectId = $projectId;

        if ($projectId) {
            $this->loadProject();
        } else {
            $this->reset(['name', 'description', 'status', 'expected_start_at', 'expected_end_at']);
            $this->project = null;
        }

        $this->activeTab = 'details';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['projectId', 'project', 'activeTab', 'newStageName']);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'expected_start_at' => $this->expected_start_at ?: null,
            'expected_end_at' => $this->expected_end_at ?: null,
        ];

        if ($this->project) {
            $this->project->update($data);
            $this->dispatch('project-updated', projectId: $this->project->id);
        }

        $this->closeModal();
    }

    public function deleteProject()
    {
        if ($this->project) {
            $projectId = $this->project->id;
            $this->project->delete();
            $this->dispatch('project-deleted', projectId: $projectId);
            $this->closeModal();
        }
    }

    public function createStage()
    {
        if (!$this->project || !$this->newStageName) {
            return;
        }

        $this->project->stages()->create([
            'tenant_id' => $this->project->tenant_id,
            'name' => $this->newStageName,
            'status' => 'planned',
            'order' => $this->project->stages()->max('order') + 1,
        ]);

        $this->newStageName = '';
        $this->loadProject();
        $this->dispatch('stage-created');
    }

    public function deleteStage($stageId)
    {
        if ($this->project) {
            $stage = $this->project->stages()->find($stageId);
            if ($stage) {
                $stage->delete();
                $this->loadProject();
                $this->dispatch('stage-deleted');
            }
        }
    }

    public function render()
    {
        return view('livewire.project-editor');
    }
}
