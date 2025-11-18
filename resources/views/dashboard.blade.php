<x-layouts.app :title="__('Dashboard')">
    <div class="flex flex-col gap-4 h-full w-full max-w-full overflow-hidden">
        <!-- Seção Superior: Minhas Tarefas (3 colunas) -->
        <div class="grid grid-cols-1 md:grid-cols-3 w-full gap-4 overflow-x-hidden md:h-1/2">
            <!-- Coluna 1: Pendentes -->
            <div class="flex w-full min-w-0 flex-col gap-4 overflow-y-auto">
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
</x-layouts.app>
