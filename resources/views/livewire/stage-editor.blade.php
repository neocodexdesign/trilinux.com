<div>
    @if($showModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4" wire:click.self="closeModal">
            <div class="flex h-[90vh] w-full max-w-6xl flex-col rounded-xl bg-neutral-900 shadow-2xl" wire:click.stop>

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-neutral-700 px-6 py-4">
                    <div>
                        <h2 class="text-xl font-semibold text-neutral-50">
                            {{ $stage ? 'Edit Stage' : 'New Stage' }}
                        </h2>
                        @if($stage && $stage->project)
                            <p class="text-sm text-neutral-400">
                                {{ $stage->project->name }}
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
                        @if($stage)
                            <button
                                wire:click="setTab('tasks')"
                                class="border-b-2 px-1 py-3 text-sm font-medium transition-colors {{ $activeTab === 'tasks' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200' }}">
                                Tasks
                                @if($stage->tasks->count() > 0)
                                    <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $stage->tasks->count() }}</span>
                                @endif
                            </button>
                            <button
                                wire:click="setTab('notes')"
                                class="border-b-2 px-1 py-3 text-sm font-medium transition-colors {{ $activeTab === 'notes' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200' }}">
                                Notes
                                @if($stage->notes->count() > 0)
                                    <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $stage->notes->count() }}</span>
                                @endif
                            </button>
                            <button
                                wire:click="setTab('attachments')"
                                class="border-b-2 px-1 py-3 text-sm font-medium transition-colors {{ $activeTab === 'attachments' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200' }}">
                                Attachments
                                @if($stage->attachments->count() > 0)
                                    <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $stage->attachments->count() }}</span>
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
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-neutral-300">Name *</label>
                                    <input
                                        wire:model="name"
                                        type="text"
                                        class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Stage name">
                                    @error('name') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-neutral-300">Order</label>
                                    <input
                                        wire:model="order"
                                        type="number"
                                        min="0"
                                        class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('order') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-neutral-300">Description</label>
                                <textarea
                                    wire:model="description"
                                    rows="4"
                                    class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Stage description"></textarea>
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
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-neutral-300">Expected Start</label>
                                    <input
                                        wire:model="expected_start_at"
                                        type="date"
                                        class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-neutral-300">Expected End</label>
                                    <input
                                        wire:model="expected_end_at"
                                        type="date"
                                        class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tasks Tab -->
                    @if($activeTab === 'tasks' && $stage)
                        <div class="space-y-4">
                            <!-- Add Task -->
                            <div class="rounded-lg bg-neutral-800/50 p-4">
                                <h4 class="mb-3 text-sm font-semibold text-neutral-300">Add New Task</h4>
                                <div class="flex gap-2">
                                    <input
                                        wire:model="newTaskName"
                                        wire:keydown.enter="createTask"
                                        type="text"
                                        class="flex-1 rounded-lg bg-neutral-800 px-4 py-2 text-sm text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Task name...">
                                    <button
                                        wire:click="createTask"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                        Add Task
                                    </button>
                                </div>
                            </div>

                            <!-- Tasks List -->
                            <div class="space-y-2">
                                @forelse($stage->tasks as $task)
                                    <div class="flex items-center justify-between rounded-lg bg-neutral-800/50 p-3">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-neutral-200">{{ $task->name }}</p>
                                            <div class="mt-1 flex items-center gap-2 text-xs text-neutral-500">
                                                <span class="rounded bg-neutral-700 px-2 py-0.5">{{ ucfirst($task->status) }}</span>
                                                @if($task->responsible)
                                                    <span>{{ $task->responsible->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button
                                                wire:click="$dispatch('openTaskEditor', { taskId: {{ $task->id }} })"
                                                class="rounded p-1 text-neutral-400 hover:bg-neutral-700 hover:text-blue-400"
                                                title="Edit task">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button
                                                wire:click="deleteTask({{ $task->id }})"
                                                wire:confirm="Are you sure?"
                                                class="rounded p-1 text-neutral-400 hover:bg-neutral-700 hover:text-red-400"
                                                title="Delete task">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="py-8 text-center text-sm text-neutral-500">No tasks yet</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- Notes Tab -->
                    @if($activeTab === 'notes' && $stage)
                        <p class="text-sm text-neutral-400">Notes for stages - Similar to TaskNotes component</p>
                    @endif

                    <!-- Attachments Tab -->
                    @if($activeTab === 'attachments' && $stage)
                        <livewire:attachment-manager :attachable="$stage" :key="'stage-attachments-'.$stage->id" />
                    @endif

                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between border-t border-neutral-700 px-6 py-4">
                    <div>
                        @if($stage)
                            <button
                                wire:click="deleteStage"
                                wire:confirm="Are you sure? This will also delete all tasks in this stage."
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                                Delete Stage
                            </button>
                        @endif
                    </div>
                    <div class="flex gap-3">
                        <button
                            wire:click="closeModal"
                            class="rounded-lg bg-neutral-700 px-4 py-2 text-sm font-medium text-neutral-200 hover:bg-neutral-600">
                            Cancel
                        </button>
                        <button
                            wire:click="save"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Save Changes
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
