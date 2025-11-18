<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" wire:click.self="closeModal">
            <div class="flex h-[90vh] w-full max-w-5xl flex-col rounded-xl bg-neutral-900 shadow-2xl" wire:click.stop>

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-neutral-700 px-6 py-4">
                    <div>
                        <h2 class="text-xl font-semibold text-neutral-50">
                            {{ $task ? 'Edit Task' : 'New Task' }}
                        </h2>
                        @if($task && $task->stage)
                            <p class="text-sm text-neutral-400">
                                {{ $task->stage->project->name }} › {{ $task->stage->name }}
                            </p>
                        @endif
                    </div>
                    <button wire:click="closeModal" class="text-neutral-400 transition-colors hover:text-neutral-200">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Tabs -->
                <div class="border-b border-neutral-700 px-6">
                    <nav class="-mb-px flex space-x-6">
                        <button
                            wire:click="setTab('details')"
                            class="border-b-2 px-1 py-3 text-sm font-medium transition-colors {{ $activeTab === 'details' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200' }}">
                            Details
                        </button>
                        @if($task)
                            <button
                                wire:click="setTab('notes')"
                                class="border-b-2 px-1 py-3 text-sm font-medium transition-colors {{ $activeTab === 'notes' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200' }}">
                                Notes
                                @if($task->notes->count() > 0)
                                    <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $task->notes->count() }}</span>
                                @endif
                            </button>
                            <button
                                wire:click="setTab('attachments')"
                                class="border-b-2 px-1 py-3 text-sm font-medium transition-colors {{ $activeTab === 'attachments' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200' }}">
                                Attachments
                                @if($task->attachments->count() > 0)
                                    <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $task->attachments->count() }}</span>
                                @endif
                            </button>
                        @endif
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="flex-1 overflow-y-auto p-6">

                    <!-- Details Tab -->
                    @if($activeTab === 'details')
                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-neutral-300">Name *</label>
                                <input
                                    wire:model="name"
                                    type="text"
                                    class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Task name">
                                @error('name') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-neutral-300">Description</label>
                                <textarea
                                    wire:model="description"
                                    rows="4"
                                    class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Task description"></textarea>
                                @error('description') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-neutral-300">Status</label>
                                    <select
                                        wire:model="status"
                                        class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="planned">Planned</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="paused">Paused</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    @error('status') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-neutral-300">Responsible</label>
                                    <select
                                        wire:model="responsible_id"
                                        class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select user</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('responsible_id') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-neutral-300">Expected Start</label>
                                    <input
                                        wire:model="expected_start_at"
                                        type="date"
                                        class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('expected_start_at') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-neutral-300">Expected End</label>
                                    <input
                                        wire:model="expected_end_at"
                                        type="date"
                                        class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('expected_end_at') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-neutral-300">Estimated Hours</label>
                                    <input
                                        wire:model="estimated_hours"
                                        type="number"
                                        step="0.5"
                                        min="0"
                                        class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('estimated_hours') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Notes Tab -->
                    @if($activeTab === 'notes' && $task)
                        <div class="space-y-4">
                            <livewire:task-notes :task="$task" :key="'editor-notes-'.$task->id" />
                        </div>
                    @endif

                    <!-- Attachments Tab -->
                    @if($activeTab === 'attachments' && $task)
                        <livewire:attachment-manager :attachable="$task" :key="'editor-attachments-'.$task->id" />
                    @endif

                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between border-t border-neutral-700 px-6 py-4">
                    <div>
                        @if($task)
                            <button
                                wire:click="deleteTask"
                                wire:confirm="Are you sure you want to delete this task?"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700">
                                Delete Task
                            </button>
                        @endif
                    </div>
                    <div class="flex gap-3">
                        <button
                            wire:click="closeModal"
                            class="rounded-lg bg-neutral-700 px-4 py-2 text-sm font-medium text-neutral-200 transition-colors hover:bg-neutral-600">
                            Cancel
                        </button>
                        <button
                            wire:click="save"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700">
                            Save Changes
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
