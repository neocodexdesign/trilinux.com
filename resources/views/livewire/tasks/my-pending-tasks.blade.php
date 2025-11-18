<div class="relative flex h-full flex-col overflow-hidden rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-950/40 to-neutral-900 shadow-lg dark:border-amber-600/20">
    <!-- Editor Components -->
    <livewire:task-editor />
    <livewire:stage-editor />
    <livewire:project-editor />

    <div class="flex items-center justify-between border-b border-amber-500/20 bg-amber-950/30 px-6 py-4 backdrop-blur-sm">
        <div class="flex items-center gap-3">
            <svg class="size-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-amber-100">Pending Tasks</h3>
        </div>
        <span class="rounded-full bg-amber-500/20 px-3 py-1.5 text-sm font-bold text-amber-300 ring-1 ring-amber-500/30">
            {{ $totalTasks }} {{ Str::plural('task', $totalTasks) }}
        </span>
    </div>

    <div class="flex-1 overflow-y-auto p-6">
        @forelse($groupedTasks as $projectIndex => $projectGroup)
            <!-- Project Header -->
            <div class="mb-6 last:mb-0" x-data="{ projectOpen: true }">
                <button @click="projectOpen = !projectOpen" @dblclick.stop="$wire.dispatch('openProjectEditor', { projectId: {{ $projectGroup['project']->id ?? 0 }} })" class="mb-3 flex w-full items-center gap-3 border-b border-amber-500/20 pb-3 text-left transition-colors hover:border-amber-500/30">
                    <svg x-show="projectOpen" class="size-5 shrink-0 text-amber-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <svg x-show="!projectOpen" class="size-5 shrink-0 text-amber-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <svg class="size-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <h4 class="text-base font-semibold text-amber-200">
                        {{ $projectGroup['project']?->name ?? 'No Project' }}
                    </h4>
                </button>

                <div x-show="projectOpen" x-collapse>
                    @foreach($projectGroup['stages'] as $stageIndex => $stageGroup)
                        <!-- Stage Header -->
                        <div class="mb-4 ml-6" x-data="{ stageOpen: true }">
                            <button @click="stageOpen = !stageOpen" @dblclick.stop="$wire.dispatch('openStageEditor', { stageId: {{ $stageGroup['stage']->id ?? 0 }} })" class="mb-3 flex w-full items-center gap-2 text-left transition-colors hover:text-amber-200">
                                <svg x-show="stageOpen" class="size-4 shrink-0 text-amber-400/70 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                <svg x-show="!stageOpen" class="size-4 shrink-0 text-amber-400/70 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <svg class="size-4 text-amber-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <h5 class="text-sm font-medium text-amber-300/80">
                                    {{ $stageGroup['stage']?->name ?? 'No Stage' }}
                                </h5>
                            </button>

                            <!-- Tasks in this Stage -->
                            <div x-show="stageOpen" x-collapse class="ml-6 space-y-3">
                            @foreach($stageGroup['tasks'] as $task)
                                <div class="group relative overflow-hidden rounded-lg bg-amber-950/20 shadow-sm backdrop-blur-sm transition-all hover:bg-amber-950/30 cursor-pointer"
                                     wire:click="$dispatch('openTaskEditor', { taskId: {{ $task->id }} })">
                                    <!-- Colored vertical bar -->
                                    <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-amber-400 to-amber-600"></div>

                                    <div class="p-4 pl-6">
                                        <div class="mb-2 flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-2">
                                                <h6 class="text-base font-medium text-amber-50">
                                                    {{ $task->name }}
                                                </h6>
                                                <button
                                                    wire:click.stop="$dispatch('openTaskEditor', { taskId: {{ $task->id }} })"
                                                    class="opacity-0 group-hover:opacity-100 transition-opacity text-amber-400 hover:text-amber-300"
                                                    title="Edit task">
                                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="flex items-center gap-2" wire:click.stop>
                                                <livewire:task-notes :task="$task" :key="'task-notes-'.$task->id" />
                                                <span class="rounded px-2 py-0.5 text-xs font-medium bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30 whitespace-nowrap">
                                                    Pending
                                                </span>
                                            </div>
                                        </div>

                                        @if($task->description)
                                            <p class="mb-3 text-sm text-amber-200/60">
                                                {{ Str::limit($task->description, 150) }}
                                            </p>
                                        @endif

                                        <div class="flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-4 text-sm text-amber-300/70">
                                                @if($task->expected_start_at)
                                                    <span class="flex items-center gap-1.5">
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        Start {{ $task->expected_start_at->format('M d') }}
                                                    </span>
                                                @endif

                                                @if($task->estimated_hours)
                                                    <span class="flex items-center gap-1.5">
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $task->estimated_hours }}h
                                                    </span>
                                                @endif
                                            </div>

                                            <button
                                                wire:click.stop="startTask({{ $task->id }})"
                                                class="rounded-lg bg-amber-500 p-2 text-neutral-900 transition-all hover:bg-amber-400 active:scale-95"
                                                title="Iniciar tarefa">
                                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="flex h-full items-center justify-center text-center">
                <div class="text-center">
                    <svg class="mx-auto mb-4 size-16 text-amber-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <p class="text-base text-amber-300/50">No pending tasks</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
