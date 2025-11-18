
<div class="flex flex-col min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200 px-4 py-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Dashboard do Operador</h1>
                <p class="text-sm text-gray-500">Olá, {{ auth()->user()->name }}</p>
            </div>
            <div class="flex flex-wrap justify-end gap-2">
                <button type="button"
                        wire:click="openTaskForm"
                        class="inline-flex items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nova tarefa
                </button>
                @if(auth()->user()->canAccessFilament())
                    <a href="{{ route('switch.admin') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-md border border-blue-500 px-4 py-2 text-sm font-medium text-blue-600 transition hover:bg-blue-50">
                        Painel Admin
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline-flex">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-hidden">
        <div class="flex h-full flex-col lg:flex-row">
            <div class="w-full flex-shrink-0 border-b border-gray-200 bg-white lg:w-64 lg:border-b-0 lg:border-r">
                <div class="border-b border-gray-100 px-4 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">Organizar cards</h2>
                    <p class="text-sm text-gray-500">Toque para exibir ou esconder as colunas.</p>
                </div>
                @php
                    $cardConfig = [
                        'pendentes' => ['label' => 'Pendentes', 'count' => count($tasks['pendentes'])],
                        'executando' => ['label' => 'Executando', 'count' => count($tasks['executando'])],
                        'pausadas' => ['label' => 'Pausadas', 'count' => count($tasks['pausadas'])],
                        'concluidas' => ['label' => 'Concluídas', 'count' => count($tasks['concluidas'])],
                    ];
                @endphp
                <nav class="grid grid-cols-2 gap-2 px-4 py-4 sm:grid-cols-3 lg:grid-cols-1">
                    @foreach($cardConfig as $cardType => $config)
                        <button wire:click="toggleCard('{{ $cardType }}')"
                                class="flex items-center justify-between rounded-xl border px-3 py-2 text-left text-sm transition {{ in_array($cardType, $visibleCards) ? 'border-indigo-200 bg-indigo-50 text-indigo-900 shadow-sm' : 'border-gray-200 hover:bg-gray-50' }}">
                            <div class="flex min-w-0 items-center">
                                <span class="mr-2 text-indigo-500">
                                    @switch($cardType)
                                        @case('pendentes')
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            @break
                                        @case('executando')
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"></path>
                                            </svg>
                                            @break
                                        @case('pausadas')
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6M5 5v14a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2z"></path>
                                            </svg>
                                            @break
                                        @default
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                    @endswitch
                                </span>
                                <span class="truncate font-medium">{{ $config['label'] }}</span>
                            </div>
                            <span class="rounded-full bg-white px-2 py-1 text-xs font-semibold text-gray-600">
                                {{ $config['count'] }}
                            </span>
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="flex-1 overflow-auto">
                <div class="space-y-3 p-4">
                    @if (session()->has('success'))
                        <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="-mx-4 flex flex-nowrap gap-4 overflow-x-auto px-4 pb-4 snap-x snap-mandatory md:mx-0 md:grid md:grid-cols-2 md:gap-4 md:overflow-visible md:px-0 md:pb-0 md:snap-normal xl:grid-cols-3 2xl:grid-cols-4">
                        @if(in_array('pendentes', $visibleCards))
                            <section class="flex w-[90vw] min-w-[18rem] snap-start flex-col rounded-2xl border border-yellow-100 bg-white shadow-sm md:w-full md:min-w-0 md:snap-align-none">
                                <div class="flex items-center justify-between rounded-t-2xl border-b border-yellow-100 bg-yellow-50 px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h2 class="text-sm font-semibold text-yellow-900">Pendentes ({{ count($tasks['pendentes']) }})</h2>
                                    </div>
                                    <button wire:click="closeCard('pendentes')" class="text-gray-400 transition hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex-1 space-y-3 px-4 py-4 md:max-h-[28rem] md:overflow-y-auto">
                                    @forelse($tasks['pendentes'] as $task)
                                        <article class="rounded-xl border border-gray-100 p-3 shadow-sm transition hover:border-gray-200 hover:bg-gray-50">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-semibold text-gray-900">{{ $task->name }}</p>
                                                    <p class="mt-1 truncate text-xs text-gray-500">{{ $task->stage->project->name }} › {{ $task->stage->name }}</p>
                                                    @if($task->responsible)
                                                        <p class="mt-1 text-xs text-indigo-600">{{ $task->responsible->name }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right text-xs text-gray-500">
                                                    @if($task->estimated_hours)
                                                        <p>{{ $task->estimated_hours }}h</p>
                                                    @endif
                                                    @if($task->expected_start_at)
                                                        <p>{{ $task->expected_start_at->format('d/m H:i') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <button wire:click="previewTask({{ $task->id }})"
                                                        class="w-full rounded-md bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 transition hover:bg-blue-100 sm:w-auto">
                                                    Visualizar
                                                </button>
                                                <button wire:click="executeTask({{ $task->id }})"
                                                        class="w-full rounded-md bg-green-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-green-600 sm:w-auto">
                                                    Executar
                                                </button>
                                            </div>
                                        </article>
                                    @empty
                                        <p class="py-8 text-center text-sm text-gray-500">Nenhuma tarefa pendente.</p>
                                    @endforelse
                                </div>
                            </section>
                        @endif
                        @if(in_array('executando', $visibleCards))
                            <section class="flex w-[90vw] min-w-[18rem] snap-start flex-col rounded-2xl border border-emerald-100 bg-white shadow-sm md:w-full md:min-w-0 md:snap-align-none">
                                <div class="flex items-center justify-between rounded-t-2xl border-b border-emerald-100 bg-emerald-50 px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"></path>
                                        </svg>
                                        <h2 class="text-sm font-semibold text-emerald-900">Executando ({{ count($tasks['executando']) }})</h2>
                                    </div>
                                    <button wire:click="closeCard('executando')" class="text-gray-400 transition hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex-1 space-y-3 px-4 py-4 md:max-h-[28rem] md:overflow-y-auto">
                                    @forelse($tasks['executando'] as $task)
                                        <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                                            <p class="text-sm font-semibold text-gray-900">{{ $task->name }}</p>
                                            <p class="mt-1 text-xs text-gray-600">{{ $task->stage->project->name }} › {{ $task->stage->name }}</p>

                                            @if($task->activeTimes->count())
                                                @php
                                                    $activeTime = $task->activeTimes->first();
                                                    $workingMinutes = $activeTime->started_at->diffInMinutes(now()) - $activeTime->pause_minutes;
                                                @endphp
                                                <p class="mt-2 inline-flex items-center gap-2 rounded-md bg-white px-2 py-1 text-[11px] font-semibold text-emerald-700">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 6a9 9 0 110 12 9 9 0 010-12z"></path>
                                                    </svg>
                                                    Trabalhando: {{ floor($workingMinutes / 60) }}h {{ $workingMinutes % 60 }}m
                                                </p>
                                            @endif

                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <button wire:click="pauseTask({{ $task->id }})"
                                                        class="w-full rounded-md bg-yellow-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-yellow-600 sm:w-auto">
                                                    Pausar
                                                </button>
                                                <button wire:click="finishTask({{ $task->id }})"
                                                        class="w-full rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-emerald-700 sm:w-auto">
                                                    Concluir
                                                </button>
                                            </div>
                                        </article>
                                    @empty
                                        <p class="py-8 text-center text-sm text-gray-500">Nenhuma tarefa em execução.</p>
                                    @endforelse
                                </div>
                            </section>
                        @endif
                        @if(in_array('pausadas', $visibleCards))
                            <section class="flex w-[90vw] min-w-[18rem] snap-start flex-col rounded-2xl border border-gray-200 bg-white shadow-sm md:w-full md:min-w-0 md:snap-align-none">
                                <div class="flex items-center justify-between rounded-t-2xl border-b border-gray-200 bg-gray-50 px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6M5 5v14a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2z"></path>
                                        </svg>
                                        <h2 class="text-sm font-semibold text-gray-900">Pausadas ({{ count($tasks['pausadas']) }})</h2>
                                    </div>
                                    <button wire:click="closeCard('pausadas')" class="text-gray-400 transition hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex-1 space-y-3 px-4 py-4 md:max-h-[28rem] md:overflow-y-auto">
                                    @forelse($tasks['pausadas'] as $task)
                                        <article class="rounded-xl border border-gray-100 p-3 shadow-sm">
                                            <p class="text-sm font-semibold text-gray-900">{{ $task->name }}</p>
                                            <p class="mt-1 text-xs text-gray-600">{{ $task->stage->project->name }} › {{ $task->stage->name }}</p>
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <button wire:click="previewTask({{ $task->id }})"
                                                        class="w-full rounded-md bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 transition hover:bg-blue-100 sm:w-auto">
                                                    Visualizar
                                                </button>
                                                <button wire:click="resumeTask({{ $task->id }})"
                                                        class="w-full rounded-md bg-green-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-green-600 sm:w-auto">
                                                    Retomar
                                                </button>
                                            </div>
                                        </article>
                                    @empty
                                        <p class="py-8 text-center text-sm text-gray-500">Nenhuma tarefa pausada.</p>
                                    @endforelse
                                </div>
                            </section>
                        @endif
                        @if(in_array('concluidas', $visibleCards))
                            <section class="flex w-[90vw] min-w-[18rem] snap-start flex-col rounded-2xl border border-emerald-200 bg-white shadow-sm md:w-full md:min-w-0 md:snap-align-none">
                                <div class="flex items-center justify-between rounded-t-2xl border-b border-emerald-100 bg-emerald-50 px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <h2 class="text-sm font-semibold text-emerald-900">Concluídas ({{ count($tasks['concluidas']) }})</h2>
                                    </div>
                                    <button wire:click="closeCard('concluidas')" class="text-gray-400 transition hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex-1 space-y-3 px-4 py-4 md:max-h-[28rem] md:overflow-y-auto">
                                    @forelse($tasks['concluidas'] as $task)
                                        @php
                                            $userTaskTimes = $task->taskTimes->where('user_id', Auth::id());
                                            $firstStart = $userTaskTimes->min('started_at');
                                            $lastEnd = $userTaskTimes->max('ended_at');
                                            $totalMinutes = $firstStart && $lastEnd ? $firstStart->diffInMinutes($lastEnd) : 0;
                                            $totalPauseMinutes = $userTaskTimes->sum('pause_minutes');
                                            $effectiveMinutes = max($totalMinutes - $totalPauseMinutes, 0);
                                        @endphp
                                        <article class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                                            <p class="text-sm font-semibold text-gray-900">{{ $task->name }}</p>
                                            <p class="mt-1 text-xs text-gray-600 truncate">{{ $task->stage->project->name }}</p>
                                            <dl class="mt-3 grid grid-cols-3 gap-2 text-[11px] font-semibold">
                                                <div class="rounded-md bg-white px-2 py-1 text-blue-700">
                                                    <dt>Total</dt>
                                                    <dd>{{ floor($totalMinutes / 60) }}h {{ $totalMinutes % 60 }}m</dd>
                                                </div>
                                                <div class="rounded-md bg-white px-2 py-1 text-yellow-700">
                                                    <dt>Pausado</dt>
                                                    <dd>{{ floor($totalPauseMinutes / 60) }}h {{ $totalPauseMinutes % 60 }}m</dd>
                                                </div>
                                                <div class="rounded-md bg-white px-2 py-1 text-emerald-700">
                                                    <dt>Efetivo</dt>
                                                    <dd>{{ floor($effectiveMinutes / 60) }}h {{ $effectiveMinutes % 60 }}m</dd>
                                                </div>
                                            </dl>
                                        </article>
                                    @empty
                                        <p class="py-8 text-center text-sm text-gray-500">Nenhuma tarefa concluída.</p>
                                    @endforelse
                                </div>
                            </section>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($showTaskForm)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 px-4 py-6" wire:click="closeTaskForm">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-xl" wire:click.stop>
                <div class="flex items-center justify-between border-b px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Cadastrar nova tarefa</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" wire:click="closeTaskForm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form wire:submit.prevent="createTask" class="space-y-4 px-6 py-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Projeto</label>
                            <select wire:model="newTask.project_id" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecione um projeto</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('newTask.project_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Etapa</label>
                            <select wire:model="newTask.stage_id"
                                    @disabled(empty($newTask['project_id']))
                                    class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100">
                                <option value="">Selecione uma etapa</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                @endforeach
                            </select>
                            @error('newTask.stage_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Nome da tarefa</label>
                        <input type="text" wire:model.defer="newTask.name"
                               class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Ex: Registrar contrato" />
                        @error('newTask.name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Descrição</label>
                        <textarea rows="3" wire:model.defer="newTask.description"
                                  class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Inclua os detalhes necessários"></textarea>
                        @error('newTask.description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Responsável</label>
                            <select wire:model.defer="newTask.responsible_id"
                                    class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Qualquer membro</option>
                                @foreach($teamMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                            @error('newTask.responsible_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Estimativa (horas)</label>
                            <input type="number" step="0.5" min="0" wire:model.defer="newTask.estimated_hours"
                                   class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Ex: 2.5" />
                            @error('newTask.estimated_hours')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Início previsto</label>
                            <input type="datetime-local" wire:model.defer="newTask.expected_start_at"
                                   class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('newTask.expected_start_at')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Entrega prevista</label>
                            <input type="datetime-local" wire:model.defer="newTask.expected_end_at"
                                   class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('newTask.expected_end_at')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-wrap justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeTaskForm"
                                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar tarefa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    @if($showPreview && $selectedTask)
        <div class="fixed inset-0 z-30 flex items-center justify-center bg-black/50 px-4 py-6" wire:click="closePreview">
            <div class="w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-xl" wire:click.stop>
                <div class="flex items-start justify-between border-b px-6 py-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $selectedTask->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $selectedTask->stage->project->name }} › {{ $selectedTask->stage->name }}</p>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600" wire:click="closePreview">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4 px-6 py-6">
                    @if($selectedTask->description)
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Descrição</h4>
                            <p class="mt-1 whitespace-pre-wrap text-sm text-gray-700">{{ $selectedTask->description }}</p>
                        </div>
                    @endif
                    @if($selectedTask->responsible)
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Responsável</h4>
                            <p class="mt-1 text-sm text-gray-700">{{ $selectedTask->responsible->name }}</p>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @if($selectedTask->estimated_hours)
                            <div class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                                <p class="text-xs text-gray-500">Estimativa</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $selectedTask->estimated_hours }}h</p>
                            </div>
                        @endif
                        @if($selectedTask->expected_start_at)
                            <div class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                                <p class="text-xs text-gray-500">Início previsto</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $selectedTask->expected_start_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-wrap justify-end gap-2 pt-2">
                        <button wire:click="closePreview" type="button"
                                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100">
                            Fechar
                        </button>
                        @if($selectedTask->status === 'planned' || $selectedTask->status === 'paused')
                            <button wire:click="executeTask({{ $selectedTask->id }})" type="button"
                                    class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                {{ $selectedTask->status === 'planned' ? 'Iniciar' : 'Retomar' }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
