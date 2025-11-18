<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TaskTime;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskReports extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $selectedProject = '';
    public $selectedUser = '';
    public $reportType = 'daily_summary';
    
    public function mount()
    {
        $this->startDate = now()->subDays(7)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedSelectedProject()
    {
        $this->resetPage();
    }

    public function updatedSelectedUser()
    {
        $this->resetPage();
    }

    public function updatedReportType()
    {
        $this->resetPage();
    }

    public function getProjectsProperty()
    {
        $user = Auth::user();
        
        if ($user->role === 'superuser') {
            return Project::all();
        }
        
        return Project::where('tenant_id', $user->tenant_id)->get();
    }

    public function getUsersProperty()
    {
        $user = Auth::user();
        
        if ($user->role === 'superuser') {
            return User::whereIn('role', ['operator', 'manager'])->get();
        }
        
        if ($user->role === 'admin') {
            return User::where('tenant_id', $user->tenant_id)
                ->whereIn('role', ['operator', 'manager'])
                ->get();
        }
        
        if ($user->role === 'manager') {
            return User::where('tenant_id', $user->tenant_id)
                ->where('role', 'operator')
                ->get();
        }
        
        return collect();
    }

    public function getDailySummaryProperty()
    {
        $query = TaskTime::with(['task.stage.project', 'user'])
            ->whereBetween('started_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);

        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $query->whereHas('task', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        } elseif ($user->role === 'manager') {
            $query->whereHas('task', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })->where('user_id', $user->id);
        }

        if ($this->selectedProject) {
            $query->whereHas('task.stage', function($q) {
                $q->where('project_id', $this->selectedProject);
            });
        }

        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }

        return $query->get()
            ->groupBy(function($item) {
                return $item->started_at->format('Y-m-d');
            })
            ->map(function($dayItems) {
                return $dayItems->groupBy('user_id')->map(function($userItems, $userId) {
                    $user = $userItems->first()->user;
                    return [
                        'user' => $user,
                        'total_work_minutes' => $userItems->sum('work_minutes'),
                        'total_pause_minutes' => $userItems->sum('pause_minutes'),
                        'tasks_count' => $userItems->unique('task_id')->count(),
                        'projects' => $userItems->groupBy('task.stage.project.name')
                            ->map(function($projectItems, $projectName) {
                                return [
                                    'name' => $projectName,
                                    'work_minutes' => $projectItems->sum('work_minutes'),
                                    'tasks_count' => $projectItems->unique('task_id')->count(),
                                ];
                            })
                    ];
                });
            });
    }

    public function getProductivitySummaryProperty()
    {
        $query = TaskTime::with(['task.stage.project', 'user'])
            ->whereBetween('started_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);

        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $query->whereHas('task', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        } elseif ($user->role === 'manager') {
            $query->whereHas('task', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        }

        if ($this->selectedProject) {
            $query->whereHas('task.stage', function($q) {
                $q->where('project_id', $this->selectedProject);
            });
        }

        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }

        return $query->get()
            ->groupBy('user_id')
            ->map(function($userItems, $userId) {
                $user = $userItems->first()->user;
                $totalWorkMinutes = $userItems->sum('work_minutes');
                $totalPauseMinutes = $userItems->sum('pause_minutes');
                $totalMinutes = $totalWorkMinutes + $totalPauseMinutes;
                
                return [
                    'user' => $user,
                    'total_work_minutes' => $totalWorkMinutes,
                    'total_pause_minutes' => $totalPauseMinutes,
                    'productivity_rate' => $totalMinutes > 0 ? round(($totalWorkMinutes / $totalMinutes) * 100, 1) : 0,
                    'tasks_completed' => $userItems->where('task.status', 'completed')->unique('task_id')->count(),
                    'tasks_in_progress' => $userItems->where('task.status', 'in_progress')->unique('task_id')->count(),
                    'average_task_time' => $userItems->unique('task_id')->count() > 0 ? 
                        round($totalWorkMinutes / $userItems->unique('task_id')->count(), 1) : 0,
                ];
            })
            ->sortByDesc('productivity_rate');
    }

    public function render()
    {
        return view('livewire.admin.task-reports', [
            'projects' => $this->projects,
            'users' => $this->users,
            'dailySummary' => $this->reportType === 'daily_summary' ? $this->dailySummary : null,
            'productivitySummary' => $this->reportType === 'productivity' ? $this->productivitySummary : null,
        ]);
    }
}
