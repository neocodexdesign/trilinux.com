<div x-data="{
    cardExpanded: true,
    expandedProjects: {},
    expandedStages: {},
    expandedTasks: {}
}" class="relative flex flex-col overflow-hidden rounded-xl border border-orange-500/30 bg-gradient-to-br from-orange-950/40 to-neutral-900 shadow-lg dark:border-orange-600/20">
    <div class="flex items-center justify-between border-b border-orange-500/20 bg-orange-950/30 px-4 py-3 backdrop-blur-sm cursor-pointer hover:bg-orange-950/40 transition-colors"
         @click="cardExpanded = !cardExpanded">
        <div class="flex items-center gap-2">
            <!-- Ícone de expand/collapse do card -->
            <svg x-show="cardExpanded" class="size-4 text-orange-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <svg x-show="!cardExpanded" class="size-4 text-orange-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>

            <svg class="size-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <h3 class="text-sm font-semibold text-orange-100">My Paused Tasks</h3>
        </div>
        <span class="rounded-full bg-orange-500/20 px-2.5 py-1 text-xs font-bold text-orange-300 ring-1 ring-orange-500/30">
            {{ $totalTasks }}
        </span>
    </div>

    <div x-show="cardExpanded" x-collapse class="flex-1 p-4">
        @forelse($groupedTasks as $index => $projectGroup)
            @php
                $projectId = $projectGroup['project']?->id ?? 'no-project-' . $index;
            @endphp
            <!-- Project Header -->
            <div class="mb-4 last:mb-0" x-init="expandedProjects['{{ $projectId }}'] = true">
                <div class="mb-2 flex items-center gap-2 border-b border-orange-500/20 pb-2 cursor-pointer hover:bg-orange-950/10 transition-colors rounded px-2 py-1"
                     @click="expandedProjects['{{ $projectId }}'] = !expandedProjects['{{ $projectId }}']">
                    <!-- Ícone de expand/collapse do projeto -->
                    <svg x-show="expandedProjects['{{ $projectId }}']" class="size-3.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <svg x-show="!expandedProjects['{{ $projectId }}']" class="size-3.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>

                    <svg class="size-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <h4 class="text-sm font-semibold text-orange-200">
                        {{ $projectGroup['project']?->name ?? 'No Project' }}
                    </h4>
                    <span class="ml-auto text-xs text-orange-400/60">({{ count($projectGroup['stages']) }} stages)</span>
                </div>

                <div x-show="expandedProjects['{{ $projectId }}']" x-collapse>
                @foreach($projectGroup['stages'] as $stageIndex => $stageGroup)
                    @php
                        $stageId = $stageGroup['stage']?->id ?? 'no-stage-' . $projectId . '-' . $stageIndex;
                    @endphp
                    <!-- Stage Header -->
                    <div class="mb-3 ml-4" x-init="expandedStages['{{ $stageId }}'] = false">
                        <div class="mb-2 flex items-center gap-2 cursor-pointer hover:bg-orange-950/10 transition-colors rounded px-2 py-1"
                             @click="expandedStages['{{ $stageId }}'] = !expandedStages['{{ $stageId }}']">
                            <!-- Ícone de expand/collapse do estágio -->
                            <svg x-show="expandedStages['{{ $stageId }}']" class="size-3 text-orange-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <svg x-show="!expandedStages['{{ $stageId }}']" class="size-3 text-orange-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>

                            <svg class="size-3.5 text-orange-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h5 class="text-xs font-medium text-orange-300/80">
                                {{ $stageGroup['stage']?->name ?? 'No Stage' }}
                            </h5>
                            <span class="ml-auto text-xs text-orange-400/50">({{ count($stageGroup['tasks']) }} tasks)</span>
                        </div>

                        <!-- Tasks in this Stage -->
                        <div x-show="expandedStages['{{ $stageId }}']" x-collapse class="ml-4 space-y-2">
                            @foreach($stageGroup['tasks'] as $task)
                                <div class="group relative overflow-hidden rounded-lg bg-orange-950/20 shadow-sm backdrop-blur-sm transition-all hover:bg-orange-950/30"
                                     x-init="expandedTasks[{{ $task->id }}] = false">
                                    <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-orange-400 to-orange-600"></div>
                                    <!-- Task Header (sempre visível) -->
                                    <div class="p-3 pl-5 flex items-center justify-between cursor-pointer"
                                         @click="expandedTasks[{{ $task->id }}] = !expandedTasks[{{ $task->id }}]">
                                        <div class="flex items-center gap-2 flex-1">
                                            <!-- Ícone de expand/collapse da tarefa -->
                                            <svg x-show="expandedTasks[{{ $task->id }}]" class="size-3 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                            <svg x-show="!expandedTasks[{{ $task->id }}]" class="size-3 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>

                                            <h6 class="text-sm font-medium text-orange-50">
                                                {{ $task->name }}
                                            </h6>
                                            <span class="ml-2 rounded px-1.5 py-0.5 text-xs font-medium
                                                {{ $task->status === 'in_progress' ? 'bg-orange-500/20 text-orange-300 ring-1 ring-orange-500/30' : 'bg-yellow-500/20 text-yellow-300 ring-1 ring-yellow-500/30' }}">
                                                {{ $task->status === 'in_progress' ? 'Active' : 'Paused' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Task Details (minimizável) -->
                                    <div x-show="expandedTasks[{{ $task->id }}]" x-collapse class="px-3 pl-5 pb-3">
                                        <div class="mb-3">
                                            <div class="flex items-center gap-2 text-xs text-orange-300/70">
                                                @if($task->started_at)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        Started {{ $task->started_at->diffForHumans() }}
                                                    </span>
                                                @endif

                                                @if($task->estimated_hours)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                        </svg>
                                                        {{ $task->estimated_hours }}h est.
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Botões de Ação (só aparecem quando expandido) -->
                                        <div class="flex gap-2 mt-3 pt-3 border-t border-orange-500/20">
                                            <button
                                                wire:click.stop="resumeTask({{ $task->id }})"
                                                @click.stop
                                                class="rounded bg-green-500/30 p-2 text-white transition-all hover:bg-green-500/50 active:scale-95"
                                                title="Retomar">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="flex h-32 items-center justify-center text-center">
                <p class="text-sm text-orange-300/50">No paused tasks</p>
            </div>
        @endforelse
    </div>
</div>
