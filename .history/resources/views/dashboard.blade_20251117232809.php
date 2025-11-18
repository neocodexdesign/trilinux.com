<x-layouts.app :title="__('Dashboard')">
    <div class="flex flex-col gap-4 h-full w-full max-w-full overflow-hidden">
        <!-- Seção Superior: Minhas Tarefas (3 colunas) -->
<<<<<<< HEAD
        <div class="flex flex-col md:flex-row w-full gap-4 overflow-x-hidden md:h-1/2">
            <!-- Coluna 1: Pendentes -->
            <div class="flex w-full md:w-1/3 min-w-0 md:max-w-[33.333%] flex-col gap-4 overflow-y-auto">
=======
        <div class="flex w-full gap-4 overflow-x-hidden" style="height: 50%;">
            <!-- Coluna 1: Pendentes -->
            <div class="flex w-1/3 min-w-0 max-w-[33.333%] flex-col gap-4 overflow-y-auto">
>>>>>>> 0b3ae898677ff6f40fb5c115fcbeadc6d5b60c58
                <livewire:dashboard.pending-tasks />
            </div>

            <!-- Coluna 2: Ativas -->
<<<<<<< HEAD
            <div class="flex w-full md:w-1/3 min-w-0 md:max-w-[33.333%] flex-col gap-4 overflow-y-auto">
=======
            <div class="flex w-1/3 min-w-0 max-w-[33.333%] flex-col gap-4 overflow-y-auto">
>>>>>>> 0b3ae898677ff6f40fb5c115fcbeadc6d5b60c58
                <livewire:dashboard.ongoing-tasks />
            </div>

            <!-- Coluna 3: Pausadas e Completadas -->
<<<<<<< HEAD
            <div class="flex w-full md:w-1/3 min-w-0 md:max-w-[33.333%] flex-col gap-4 overflow-y-auto">
=======
            <div class="flex w-1/3 min-w-0 max-w-[33.333%] flex-col gap-4 overflow-y-auto">
>>>>>>> 0b3ae898677ff6f40fb5c115fcbeadc6d5b60c58
                <livewire:dashboard.paused-tasks />
                <livewire:dashboard.completed-tasks />
            </div>
        </div>

        <!-- Seção Inferior: Tarefas por Equipes -->
<<<<<<< HEAD
        <div class="flex flex-col w-full gap-4 overflow-x-hidden border-t border-neutral-700/50 pt-4 md:h-1/2">
=======
        <div class="flex w-full gap-4 overflow-x-hidden border-t border-neutral-700/50 pt-4" style="height: 50%;">
>>>>>>> 0b3ae898677ff6f40fb5c115fcbeadc6d5b60c58
            <livewire:dashboard.team-tasks />
        </div>
    </div>
</x-layouts.app>
