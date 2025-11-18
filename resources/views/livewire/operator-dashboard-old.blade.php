<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-lg border-r border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Dashboard</h2>
        </div>
        
        <nav class="p-4 space-y-2">
            @php
                $cardConfig = [
                    'pendentes' => ['icon' => '🟡', 'label' => 'Pendentes', 'count' => count($tasks['pendentes'])],
                    'executando' => ['icon' => '🟢', 'label' => 'Executando', 'count' => count($tasks['executando'])],
                    'pausadas' => ['icon' => '⏸️', 'label' => 'Pausadas', 'count' => count($tasks['pausadas'])],
                    'concluidas' => ['icon' => '✅', 'label' => 'Concluídas', 'count' => count($tasks['concluidas'])]
                ];
            @endphp
            
            @foreach($cardConfig as $cardType => $config)
                <button wire:click="toggleCard('{{ $cardType }}')" 
                        class="w-full flex items-center justify-between p-3 rounded-lg text-left transition
                               {{ in_array($cardType, $visibleCards) ? 'bg-blue-50 border-blue-200 text-blue-800' : 'hover:bg-gray-50 text-gray-700' }}">
                    <div class="flex items-center">
                        <span class="text-lg mr-3">{{ $config['icon'] }}</span>
                        <span class="font-medium">{{ $config['label'] }}</span>
                    </div>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                        {{ $config['count'] }}
                    </span>
                </button>
            @endforeach
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="m-4 p-4 bg-green-50 border border-green-200 rounded-md">
                <div class="text-green-700">{{ session('success') }}</div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="m-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <div class="text-red-700">{{ session('error') }}</div>
            </div>
        @endif

        <!-- Cards Grid -->
        <div class="p-4 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            
            <!-- Card Pendentes -->
            @if(in_array('pendentes', $visibleCards))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="bg-yellow-50 border-b border-yellow-200 px-4 py-3 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-yellow-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            Pendentes ({{ count($tasks['pendentes']) }})
                        </h2>
                        <button wire:click="closeCard('pendentes')" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
        <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
            @forelse($tasks['pendentes'] as $task)
                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900 text-sm">{{ $task->name }}</h3>
                            <p class="text-xs text-gray-600 mt-1">{{ $task->stage->project->name }} > {{ $task->stage->name }}</p>
                            @if($task->responsible)
                                <p class="text-xs text-blue-600 mt-1">{{ $task->responsible->name }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            @if($task->estimated_hours)
                                <span class="text-xs text-gray-500">{{ $task->estimated_hours }}h</span>
                            @endif
                            @if($task->expected_start_at)
                                <p class="text-xs text-gray-500 mt-1">{{ $task->expected_start_at->format('d/m H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-2 flex space-x-2">
                        <button wire:click="previewTask({{ $task->id }})" 
                                class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition">
                            Visualizar
                        </button>
                        <button wire:click="executeTask({{ $task->id }})" 
                                class="text-xs bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 transition">
                            Executar
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">Nenhuma tarefa pendente</p>
                    @endforelse
                    </div>
                </div>
            @endif

            <!-- Card Executando -->
            @if(in_array('executando', $visibleCards))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="bg-green-50 border-b border-green-200 px-4 py-3 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-green-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Executando ({{ count($tasks['executando']) }})
                        </h2>
                        <button wire:click="closeCard('executando')" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
        <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
            @forelse($tasks['executando'] as $task)
                <div class="border border-green-200 rounded-lg p-3 bg-green-50">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900 text-sm">{{ $task->name }}</h3>
                            <p class="text-xs text-gray-600 mt-1">{{ $task->stage->project->name }} > {{ $task->stage->name }}</p>
                        </div>
                        <div class="flex space-x-1">
                            <button wire:click="pauseTask({{ $task->id }})" 
                                    class="text-xs bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 transition">
                                Pausar
                            </button>
                            <button wire:click="finishTask({{ $task->id }})" 
                                    class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition">
                                Concluir
                            </button>
                        </div>
                    </div>
                    @if($task->activeTimes->count())
                        @php
                            $activeTime = $task->activeTimes->first();
                            $workingMinutes = $activeTime->started_at->diffInMinutes(now()) - $activeTime->pause_minutes;
                        @endphp
                        <div class="text-xs text-green-700 font-mono bg-green-100 px-2 py-1 rounded">
                            Trabalhando: {{ floor($workingMinutes / 60) }}h {{ $workingMinutes % 60 }}m
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">Nenhuma tarefa em execução</p>
            @endforelse
        </div>
    </div>

    <!-- Tarefas Pausadas -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-gray-50 border-b border-gray-200 px-4 py-3">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Pausadas ({{ count($tasks['pausadas']) }})
            </h2>
        </div>
        <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
            @forelse($tasks['pausadas'] as $task)
                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900 text-sm">{{ $task->name }}</h3>
                            <p class="text-xs text-gray-600 mt-1">{{ $task->stage->project->name }} > {{ $task->stage->name }}</p>
                        </div>
                    </div>
                    <div class="mt-2 flex space-x-2">
                        <button wire:click="previewTask({{ $task->id }})" 
                                class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition">
                            Visualizar
                        </button>
                        <button wire:click="resumeTask({{ $task->id }})" 
                                class="text-xs bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 transition">
                            Retomar
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">Nenhuma tarefa pausada</p>
            @endforelse
        </div>
    </div>

    <!-- Tarefas Concluídas -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-green-50 border-b border-green-200 px-4 py-3">
            <h2 class="text-lg font-semibold text-green-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Concluídas ({{ count($tasks['concluidas']) }})
            </h2>
        </div>
        <div class="p-4 space-y-2 max-h-96 overflow-y-auto">
            @forelse($tasks['concluidas'] as $task)
                @php
                    $userTaskTimes = $task->taskTimes->where('user_id', Auth::id());
                    
                    // Calcula tempo total (início da primeira sessão até fim da última)
                    $firstStart = $userTaskTimes->min('started_at');
                    $lastEnd = $userTaskTimes->max('ended_at');
                    $totalMinutes = $firstStart && $lastEnd ? $firstStart->diffInMinutes($lastEnd) : 0;
                    
                    // Total de pausas
                    $totalPauseMinutes = $userTaskTimes->sum('pause_minutes');
                    
                    // Tempo efetivo = total - pausas
                    $effectiveMinutes = $totalMinutes - $totalPauseMinutes;
                @endphp
                <div class="border border-green-200 rounded-lg p-2 bg-green-50">
                    <div class="flex justify-between items-center">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 text-sm truncate">{{ $task->name }}</h3>
                            <p class="text-xs text-gray-600 truncate">{{ $task->stage->project->name }}</p>
                        </div>
                        <div class="flex flex-col text-right ml-3 text-xs font-mono">
                            <div class="text-blue-700">
                                🕐 {{ floor($totalMinutes / 60) }}h {{ $totalMinutes % 60 }}m
                            </div>
                            <div class="text-yellow-600">
                                ⏸️ {{ floor($totalPauseMinutes / 60) }}h {{ $totalPauseMinutes % 60 }}m
                            </div>
                            <div class="text-green-700 font-semibold">
                                ✅ {{ floor($effectiveMinutes / 60) }}h {{ $effectiveMinutes % 60 }}m
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">Nenhuma tarefa concluída</p>
            @endforelse
        </div>
    </div>

    <!-- Modal de Preview -->
    @if($showPreview && $selectedTask)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
             @click="$wire.closePreview()">
            <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-96 overflow-y-auto" 
                 @click.stop>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $selectedTask->name }}</h3>
                        <button @click="$wire.closePreview()" 
                                class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Projeto</label>
                            <p class="text-gray-900">{{ $selectedTask->stage->project->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Etapa</label>
                            <p class="text-gray-900">{{ $selectedTask->stage->name }}</p>
                        </div>

                        @if($selectedTask->description)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $selectedTask->description }}</p>
                            </div>
                        @endif

                        @if($selectedTask->responsible)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Responsável</label>
                                <p class="text-gray-900">{{ $selectedTask->responsible->name }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            @if($selectedTask->estimated_hours)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Estimativa</label>
                                    <p class="text-gray-900">{{ $selectedTask->estimated_hours }} horas</p>
                                </div>
                            @endif

                            @if($selectedTask->expected_start_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Início Previsto</label>
                                    <p class="text-gray-900">{{ $selectedTask->expected_start_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="$wire.closePreview()" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300 transition">
                            Fechar
                        </button>
                        @if($selectedTask->status === 'planned' || $selectedTask->status === 'paused')
                            <button wire:click="executeTask({{ $selectedTask->id }})" 
                                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
                                {{ $selectedTask->status === 'planned' ? 'Iniciar' : 'Retomar' }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
</div>

