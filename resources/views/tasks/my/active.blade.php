<x-layouts.app :title="__('My Active Tasks')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">My Active Tasks</h1>
        <p class="text-sm text-neutral-600 dark:text-neutral-400">View and manage all your active and paused tasks.</p>

        <div class="h-full flex-1">
            <livewire:tasks.my-active-tasks />
        </div>
    </div>
</x-layouts.app>
