<x-layouts.app :title="__('Team Ongoing Tasks')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Team Ongoing Tasks</h1>
        <p class="text-sm text-neutral-600 dark:text-neutral-400">View and manage all active tasks of your team.</p>

        <div class="h-full flex-1">
            <livewire:tasks.team-ongoing-tasks />
        </div>
    </div>
</x-layouts.app>
