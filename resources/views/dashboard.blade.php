<x-layouts.app :title="__('Dashboard')">
    @php
        $loggedUser = auth()->user();
        $canCreateTask = $loggedUser?->can('create', \App\Models\Task::class);
    @endphp

    <div class="mb-4 rounded-xl border border-amber-500/50 bg-amber-950/40 px-4 py-3 text-sm text-amber-100">
        <p><strong>Debug Usuário:</strong> {{ $loggedUser?->email }} | Role: {{ $loggedUser?->role }} | Tenant: {{ $loggedUser?->tenant_id }}</p>
        <p>Permissão para criar tarefas: <span class="{{ $canCreateTask ? 'text-green-300' : 'text-red-300' }}">{{ $canCreateTask ? 'SIM' : 'NÃO' }}</span></p>
    </div>

    <div class="flex flex-col gap-4 h-full w-full max-w-full overflow-hidden">
        <!-- Seção Superior: Minhas Tarefas (3 colunas) -->
        <div
            class="w-full overflow-x-hidden"
            style="display:flex;flex-direction:column;gap:1rem;width:100%;max-width:100%;"
        >
            <!-- Coluna 1: Pendentes -->
            <div class="flex w-full min-w-0 flex-col gap-4 overflow-y-auto">
                @can('create', \App\Models\Task::class)
                    <livewire:dashboard.create-task />
                @endcan
                <livewire:dashboard.pending-tasks />
            </div>

            <!-- Coluna 2: Ativas -->
            <div class="flex w-full min-w-0 flex-col gap-4 overflow-y-auto">
                <livewire:dashboard.ongoing-tasks />
            </div>

            <!-- Coluna 3: Pausadas e Completadas -->
            <div class="flex w-full min-w-0 flex-col gap-4 overflow-y-auto">
                <livewire:dashboard.paused-tasks />
                <livewire:dashboard.completed-tasks />
            </div>
        </div>

        <!-- Seção Inferior: Tarefas por Equipes -->
        <div class="flex flex-col w-full gap-4 overflow-x-hidden border-t border-neutral-700/50 pt-4 md:h-1/2">
            <livewire:dashboard.team-tasks />
        </div>
    </div>

    <!-- Modal de Edição de Tarefa -->
    <livewire:dashboard.edit-task />
</x-layouts.app>
