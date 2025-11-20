<div class="h-full w-full">
    @if($selectedTeam)
    <div class="flex h-full w-full flex-col gap-4 overflow-hidden md:flex-row">
        <!-- Column 1: Pending -->
        <div class="flex w-full min-w-0 flex-col overflow-hidden md:w-1/3 md:max-w-[33.333%]" x-data="{
            cardExpanded: true,
            expandedProjects: {},
            expandedStages: {},
            expandedTasks: {}
        }">
            <div class="relative flex flex-col overflow-hidden rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-950/40 to-neutral-900 shadow-lg">
                <div class="flex items-center justify-between border-b border-amber-500/20 bg-amber-950/30 px-4 py-3 backdrop-blur-sm cursor-pointer hover:bg-amber-950/40 transition-colors"
                     @click="cardExpanded = !cardExpanded">
                    <div class="flex items-center gap-2">
                        <!-- Ícone de expand/collapse do card -->
                        <svg x-show="cardExpanded" class="size-4 text-amber-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <svg x-show="!cardExpanded" class="size-4 text-amber-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>

                        <svg class="size-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-amber-100">{{ $selectedTeam->name }} - Pending</h3>
                    </div>
                    <span class="rounded-full bg-amber-500/20 px-2.5 py-1 text-xs font-bold text-amber-300 ring-1 ring-amber-500/30">
                        {{ $totalPlanned }}
                    </span>
                </div>

                <div x-show="cardExpanded" x-collapse class="flex-1 p-4">
                    @forelse($groupedPlanned as $index => $projectGroup)
                        @php
                            $projectId = $projectGroup['project']?->id ?? 'no-project-' . $index;
                        @endphp
                        <!-- Project Header -->
                        <div class="mb-4 last:mb-0" x-init="expandedProjects['{{ $projectId }}'] = true">
                            <div class="mb-2 flex items-center gap-2 border-b border-amber-500/20 pb-2 cursor-pointer hover:bg-amber-950/10 transition-colors rounded px-2 py-1"
                                 @click="expandedProjects['{{ $projectId }}'] = !expandedProjects['{{ $projectId }}']">
                                <!-- Ícone de expand/collapse do projeto -->
                                <svg x-show="expandedProjects['{{ $projectId }}']" class="size-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                <svg x-show="!expandedProjects['{{ $projectId }}']" class="size-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>

                                <svg class="size-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                <h4 class="text-sm font-semibold text-amber-200">
                                    {{ $projectGroup['project']?->name ?? 'No Project' }}
                                </h4>
                                <span class="ml-auto text-xs text-amber-400/60">({{ count($projectGroup['stages']) }} stages)</span>
                            </div>

                            <div x-show="expandedProjects['{{ $projectId }}']" x-collapse>
                            @foreach($projectGroup['stages'] as $stageIndex => $stageGroup)
                                @php
                                    $stageId = $stageGroup['stage']?->id ?? 'no-stage-' . $projectId . '-' . $stageIndex;
                                @endphp
                                <!-- Stage Header -->
                                <div class="mb-3 ml-4" x-init="expandedStages['{{ $stageId }}'] = false">
                                    <div class="mb-2 flex items-center gap-2 cursor-pointer hover:bg-amber-950/10 transition-colors rounded px-2 py-1"
                                         @click="expandedStages['{{ $stageId }}'] = !expandedStages['{{ $stageId }}']">
                                        <!-- Ícone de expand/collapse do estágio -->
                                        <svg x-show="expandedStages['{{ $stageId }}']" class="size-3 text-amber-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                        <svg x-show="!expandedStages['{{ $stageId }}']" class="size-3 text-amber-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>

                                        <svg class="size-3.5 text-amber-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <h5 class="text-xs font-medium text-amber-300/80">
                                            {{ $stageGroup['stage']?->name ?? 'No Stage' }}
                                        </h5>
                                        <span class="ml-auto text-xs text-amber-400/50">({{ count($stageGroup['tasks']) }} tasks)</span>
                                    </div>

                                    <!-- Tasks in this Stage -->
                                    <div x-show="expandedStages['{{ $stageId }}']" x-collapse class="ml-4 space-y-2">
                                        @foreach($stageGroup['tasks'] as $task)
                                            <div class="group relative overflow-hidden rounded-lg bg-amber-950/20 shadow-sm backdrop-blur-sm transition-all hover:bg-amber-950/30"
                                                 x-init="expandedTasks[{{ $task->id }}] = false">
                                                <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-amber-400 to-amber-600"></div>
                                                <!-- Task Header (sempre visível) -->
                                                <div class="p-3 pl-5 flex items-center justify-between cursor-pointer"
                                                     @click="expandedTasks[{{ $task->id }}] = !expandedTasks[{{ $task->id }}]">
                                                    <div class="flex items-center gap-2 flex-1">
                                                        <!-- Ícone de expand/collapse da tarefa -->
                                                        <svg x-show="expandedTasks[{{ $task->id }}]" class="size-3 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                        <svg x-show="!expandedTasks[{{ $task->id }}]" class="size-3 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                        </svg>

                                                        <h6 class="text-sm font-medium text-amber-50">
                                                            {{ $task->name }}
                                                        </h6>
                                                    </div>
                                                </div>

                                                <!-- Task Details (minimizável) -->
                                                <div x-show="expandedTasks[{{ $task->id }}]" x-collapse class="px-3 pl-5 pb-3">
                                                    @if($task->estimated_hours)
                                                        <div class="mb-3">
                                                            <div class="flex items-center gap-2 text-xs text-amber-300/70">
                                                                <span class="flex items-center gap-1">
                                                                    <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    {{ $task->estimated_hours }}h est.
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Botões de Ação (só aparecem quando expandido) -->
                                                    <div class="flex gap-2 mt-3 pt-3 border-t border-amber-500/20">
                                                        <button
                                                            wire:click.stop="startTask({{ $task->id }})"
                                                            @click.stop
                                                            class="rounded bg-green-500/30 p-2 text-white transition-all hover:bg-green-500/50 active:scale-95"
                                                            title="Iniciar tarefa">
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
                            <p class="text-sm text-amber-300/50">No pending tasks</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Column 2: Ongoing -->
        <div class="flex w-full min-w-0 flex-col overflow-hidden md:w-1/3 md:max-w-[33.333%]" x-data="{
            cardExpanded: true,
            confirmingRevert: @entangle('confirmingRevert'),
            expandedProjects: {},
            expandedStages: {},
            expandedTasks: {}
        }">
            <div class="relative flex flex-col overflow-hidden rounded-xl border border-sky-500/30 bg-gradient-to-br from-sky-950/40 to-neutral-900 shadow-lg">
                <div class="flex items-center justify-between border-b border-sky-500/20 bg-sky-950/30 px-4 py-3 backdrop-blur-sm cursor-pointer hover:bg-sky-950/40 transition-colors"
                     @click="cardExpanded = !cardExpanded">
                    <div class="flex items-center gap-2">
                        <!-- Ícone de expand/collapse do card -->
                        <svg x-show="cardExpanded" class="size-4 text-sky-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <svg x-show="!cardExpanded" class="size-4 text-sky-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>

                        <svg class="size-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-sky-100">{{ $selectedTeam->name }} - Ongoing</h3>
                    </div>
                    <span class="rounded-full bg-sky-500/20 px-2.5 py-1 text-xs font-bold text-sky-300 ring-1 ring-sky-500/30">
                        {{ $totalOngoing }}
                    </span>
                </div>

                <div x-show="cardExpanded" x-collapse class="flex-1 p-4">
                    @forelse($groupedOngoing as $index => $projectGroup)
                        @php
                            $projectId = $projectGroup['project']?->id ?? 'no-project-ongoing-' . $index;
                        @endphp
                        <!-- Project Header -->
                        <div class="mb-4 last:mb-0" x-init="expandedProjects['{{ $projectId }}'] = true">
                            <div class="mb-2 flex items-center gap-2 border-b border-sky-500/20 pb-2 cursor-pointer hover:bg-sky-950/10 transition-colors rounded px-2 py-1"
                                 @click="expandedProjects['{{ $projectId }}'] = !expandedProjects['{{ $projectId }}']">
                                <!-- Ícone de expand/collapse do projeto -->
                                <svg x-show="expandedProjects['{{ $projectId }}']" class="size-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                <svg x-show="!expandedProjects['{{ $projectId }}']" class="size-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>

                                <svg class="size-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                <h4 class="text-sm font-semibold text-sky-200">
                                    {{ $projectGroup['project']?->name ?? 'No Project' }}
                                </h4>
                                <span class="ml-auto text-xs text-sky-400/60">({{ count($projectGroup['stages']) }} stages)</span>
                            </div>

                            <div x-show="expandedProjects['{{ $projectId }}']" x-collapse>
                            @foreach($projectGroup['stages'] as $stageIndex => $stageGroup)
                                @php
                                    $stageId = $stageGroup['stage']?->id ?? 'no-stage-ongoing-' . $projectId . '-' . $stageIndex;
                                @endphp
                                <!-- Stage Header -->
                                <div class="mb-3 ml-4" x-init="expandedStages['{{ $stageId }}'] = false">
                                    <div class="mb-2 flex items-center gap-2 cursor-pointer hover:bg-sky-950/10 transition-colors rounded px-2 py-1"
                                         @click="expandedStages['{{ $stageId }}'] = !expandedStages['{{ $stageId }}']">
                                        <!-- Ícone de expand/collapse do estágio -->
                                        <svg x-show="expandedStages['{{ $stageId }}']" class="size-3 text-sky-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                        <svg x-show="!expandedStages['{{ $stageId }}']" class="size-3 text-sky-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>

                                        <svg class="size-3.5 text-sky-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <h5 class="text-xs font-medium text-sky-300/80">
                                            {{ $stageGroup['stage']?->name ?? 'No Stage' }}
                                        </h5>
                                        <span class="ml-auto text-xs text-sky-400/50">({{ count($stageGroup['tasks']) }} tasks)</span>
                                    </div>

                                    <!-- Tasks in this Stage -->
                                    <div x-show="expandedStages['{{ $stageId }}']" x-collapse class="ml-4 space-y-2">
                                        @foreach($stageGroup['tasks'] as $task)
                                            <div class="group relative overflow-hidden rounded-lg bg-sky-950/20 shadow-sm backdrop-blur-sm transition-all hover:bg-sky-950/30"
                                                 x-init="expandedTasks[{{ $task->id }}] = false">
                                                <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-sky-400 to-sky-600"></div>
                                                <!-- Task Header (sempre visível) -->
                                                <div class="p-3 pl-5 flex items-center justify-between cursor-pointer"
                                                     @click="expandedTasks[{{ $task->id }}] = !expandedTasks[{{ $task->id }}]">
                                                    <div class="flex items-center gap-2 flex-1">
                                                        <!-- Ícone de expand/collapse da tarefa -->
                                                        <svg x-show="expandedTasks[{{ $task->id }}]" class="size-3 text-sky-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                        <svg x-show="!expandedTasks[{{ $task->id }}]" class="size-3 text-sky-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                        </svg>

                                                        <h6 class="text-sm font-medium text-sky-50">
                                                            {{ $task->name }}
                                                        </h6>
                                                        <span class="ml-2 rounded px-1.5 py-0.5 text-xs font-medium bg-sky-500/20 text-sky-300 ring-1 ring-sky-500/30">
                                                            Active
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Task Details (minimizável) -->
                                                <div x-show="expandedTasks[{{ $task->id }}]" x-collapse class="px-3 pl-5 pb-3">
                                                    @if($task->estimated_hours)
                                                        <div class="mb-3">
                                                            <div class="flex items-center gap-2 text-xs text-sky-300/70">
                                                                <span class="flex items-center gap-1">
                                                                    <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                                    </svg>
                                                                    {{ $task->estimated_hours }}h est.
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Botões de Ação (só aparecem quando expandido) -->
                                                    <div class="flex gap-2 mt-3 pt-3 border-t border-sky-500/20">
                                                        @php
                                                            $activeTime = $task->taskTimes->where('user_id', auth()->id())->whereNull('ended_at')->first();
                                                        @endphp

                                                        @if($activeTime)
                                                            <button
                                                                wire:click.stop="confirmRevert({{ $task->id }})"
                                                                @click.stop
                                                                class="rounded bg-red-500/30 px-2 py-1 text-xl transition-all hover:bg-red-500/50 active:scale-95"
                                                                title="Desfazer início">
                                                                ↩️
                                                            </button>
                                                        @endif

                                                        <button
                                                            wire:click.stop="completeTask({{ $task->id }})"
                                                            @click.stop
                                                            class="rounded bg-emerald-500/30 p-2 text-white transition-all hover:bg-emerald-500/50 active:scale-95"
                                                            title="Concluir">
                                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </button>
                                                        <button
                                                            wire:click.stop="pauseTask({{ $task->id }})"
                                                            @click.stop
                                                            class="rounded bg-orange-500/30 p-2 text-white transition-all hover:bg-orange-500/50 active:scale-95"
                                                            title="Pausar">
                                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                            <p class="text-sm text-sky-300/50">No ongoing tasks</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Modal de Confirmação (Revert) -->
            @if($confirmingRevert)
                <div class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/60 backdrop-blur-sm"
                     x-data
                     x-show="true"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     @click.self="$wire.cancelRevert()">
                    <div class="w-full max-w-md rounded-xl bg-gradient-to-br from-neutral-900 to-neutral-800 p-6 shadow-2xl ring-2 ring-red-500/40 border-2 border-red-500"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-500/20 ring-2 ring-red-500/30">
                                <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Confirmar Desfazer</h3>
                                <p class="text-sm text-neutral-400">Esta ação não pode ser revertida</p>
                            </div>
                        </div>

                        <p class="mb-6 text-neutral-300">
                            Tem certeza que deseja desfazer o início desta tarefa? Todo o tempo registrado será removido permanentemente.
                        </p>

                        <div class="flex gap-3">
                            <button wire:click="cancelRevert"
                                    class="flex-1 rounded-lg bg-neutral-700/90 px-4 py-2.5 font-medium text-white transition-all hover:bg-neutral-600 active:scale-95 shadow-lg">
                                Cancelar
                            </button>
                            <button wire:click="revertStart"
                                    class="flex-1 rounded-lg bg-red-500 px-4 py-2.5 font-medium text-white transition-all hover:bg-red-600 active:scale-95 shadow-lg">
                                Sim, Desfazer
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Column 3: Paused & Completed -->
        <div class="flex w-full min-w-0 flex-col gap-4 overflow-y-auto md:w-1/3 md:max-w-[33.333%]">
            <!-- Paused -->
            <div class="relative flex flex-col overflow-hidden rounded-xl border border-orange-500/30 bg-gradient-to-br from-orange-950/40 to-neutral-900 shadow-lg" x-data="{
                cardExpanded: true,
                expandedProjects: {},
                expandedStages: {},
                expandedTasks: {}
            }">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-orange-100">{{ $selectedTeam->name }} - Paused</h3>
                    </div>
                    <span class="rounded-full bg-orange-500/20 px-2.5 py-1 text-xs font-bold text-orange-300 ring-1 ring-orange-500/30">
                        {{ $totalPaused }}
                    </span>
                </div>

                <div x-show="cardExpanded" x-collapse class="flex-1 p-4">
                    @forelse($groupedPaused as $index => $projectGroup)
                        @php
                            $projectId = $projectGroup['project']?->id ?? 'no-project-paused-' . $index;
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
                                    $stageId = $stageGroup['stage']?->id ?? 'no-stage-paused-' . $projectId . '-' . $stageIndex;
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
                                                        <span class="ml-2 rounded px-1.5 py-0.5 text-xs font-medium bg-yellow-500/20 text-yellow-300 ring-1 ring-yellow-500/30">
                                                            Paused
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Task Details (minimizável) -->
                                                <div x-show="expandedTasks[{{ $task->id }}]" x-collapse class="px-3 pl-5 pb-3">
                                                    @if($task->estimated_hours)
                                                        <div class="mb-3">
                                                            <div class="flex items-center gap-2 text-xs text-orange-300/70">
                                                                <span class="flex items-center gap-1">
                                                                    <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    {{ $task->estimated_hours }}h est.
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif

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

            <!-- Completed -->
            <div class="relative flex flex-col overflow-hidden rounded-xl border border-green-500/30 bg-gradient-to-br from-green-950/40 to-neutral-900 shadow-lg" x-data="{
                cardExpanded: true,
                expandedProjects: {},
                expandedStages: {},
                expandedTasks: {}
            }">
                <div class="flex items-center justify-between border-b border-green-500/20 bg-green-950/30 px-4 py-3 backdrop-blur-sm cursor-pointer hover:bg-green-950/40 transition-colors"
                     @click="cardExpanded = !cardExpanded">
                    <div class="flex items-center gap-2">
                        <!-- Ícone de expand/collapse do card -->
                        <svg x-show="cardExpanded" class="size-4 text-green-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <svg x-show="!cardExpanded" class="size-4 text-green-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>

                        <svg class="size-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-green-100">{{ $selectedTeam->name }} - Completed</h3>
                    </div>
                    <span class="rounded-full bg-green-500/20 px-2.5 py-1 text-xs font-bold text-green-300 ring-1 ring-green-500/30">
                        {{ $totalCompleted }}
                    </span>
                </div>

                <div x-show="cardExpanded" x-collapse class="flex-1 p-4">
                    @forelse($groupedCompleted as $index => $projectGroup)
                        @php
                            $projectId = $projectGroup['project']?->id ?? 'no-project-completed-' . $index;
                        @endphp
                        <!-- Project Header -->
                        <div class="mb-4 last:mb-0" x-init="expandedProjects['{{ $projectId }}'] = true">
                            <div class="mb-2 flex items-center gap-2 border-b border-green-500/20 pb-2 cursor-pointer hover:bg-green-950/10 transition-colors rounded px-2 py-1"
                                 @click="expandedProjects['{{ $projectId }}'] = !expandedProjects['{{ $projectId }}']">
                                <!-- Ícone de expand/collapse do projeto -->
                                <svg x-show="expandedProjects['{{ $projectId }}']" class="size-3.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                <svg x-show="!expandedProjects['{{ $projectId }}']" class="size-3.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>

                                <svg class="size-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                <h4 class="text-sm font-semibold text-green-200">
                                    {{ $projectGroup['project']?->name ?? 'No Project' }}
                                </h4>
                                <span class="ml-auto text-xs text-green-400/60">({{ count($projectGroup['stages']) }} stages)</span>
                            </div>

                            <div x-show="expandedProjects['{{ $projectId }}']" x-collapse>
                            @foreach($projectGroup['stages'] as $stageIndex => $stageGroup)
                                @php
                                    $stageId = $stageGroup['stage']?->id ?? 'no-stage-completed-' . $projectId . '-' . $stageIndex;
                                @endphp
                                <!-- Stage Header -->
                                <div class="mb-3 ml-4" x-init="expandedStages['{{ $stageId }}'] = false">
                                    <div class="mb-2 flex items-center gap-2 cursor-pointer hover:bg-green-950/10 transition-colors rounded px-2 py-1"
                                         @click="expandedStages['{{ $stageId }}'] = !expandedStages['{{ $stageId }}']">
                                        <!-- Ícone de expand/collapse do estágio -->
                                        <svg x-show="expandedStages['{{ $stageId }}']" class="size-3 text-green-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                        <svg x-show="!expandedStages['{{ $stageId }}']" class="size-3 text-green-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>

                                        <svg class="size-3.5 text-green-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <h5 class="text-xs font-medium text-green-300/80">
                                            {{ $stageGroup['stage']?->name ?? 'No Stage' }}
                                        </h5>
                                        <span class="ml-auto text-xs text-green-400/50">({{ count($stageGroup['tasks']) }} tasks)</span>
                                    </div>

                                    <!-- Tasks in this Stage -->
                                    <div x-show="expandedStages['{{ $stageId }}']" x-collapse class="ml-4 space-y-2">
                                        @foreach($stageGroup['tasks'] as $task)
                                            <div class="group relative overflow-hidden rounded-lg bg-green-950/20 shadow-sm backdrop-blur-sm transition-all hover:bg-green-950/30"
                                                 x-init="expandedTasks[{{ $task->id }}] = false">
                                                <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-green-400 to-green-600"></div>
                                                <!-- Task Header (sempre visível) -->
                                                <div class="p-3 pl-5 flex items-center justify-between cursor-pointer"
                                                     @click="expandedTasks[{{ $task->id }}] = !expandedTasks[{{ $task->id }}]">
                                                    <div class="flex items-center gap-2 flex-1">
                                                        <!-- Ícone de expand/collapse da tarefa -->
                                                        <svg x-show="expandedTasks[{{ $task->id }}]" class="size-3 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                        <svg x-show="!expandedTasks[{{ $task->id }}]" class="size-3 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                        </svg>

                                                        <h6 class="text-sm font-medium text-green-50">
                                                            {{ $task->name }}
                                                        </h6>
                                                    </div>
                                                </div>

                                                <!-- Task Details (minimizável) -->
                                                <div x-show="expandedTasks[{{ $task->id }}]" x-collapse class="px-3 pl-5 pb-3">
                                                    @if($task->estimated_hours)
                                                        <div class="flex items-center gap-2 text-xs text-green-300/70">
                                                            <span class="flex items-center gap-1">
                                                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                {{ $task->estimated_hours }}h est.
                                                            </span>
                                                        </div>
                                                    @endif
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
                            <p class="text-sm text-green-300/50">No completed tasks</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="flex h-full w-full items-center justify-center rounded-xl border border-purple-500/30 bg-gradient-to-br from-purple-950/40 to-neutral-900">
        <div class="text-center">
            <svg class="mx-auto mb-4 size-16 text-purple-400/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-lg font-medium text-purple-200">Select a team</p>
            <p class="text-sm text-purple-400">Choose a team from the sidebar to see tasks</p>
        </div>
    </div>
    @endif
</div>
