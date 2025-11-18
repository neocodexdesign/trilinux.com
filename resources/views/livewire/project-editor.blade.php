<div>
    @if($showModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 p-4" wire:click.self="closeModal">
            <div class="flex h-[90vh] w-full max-w-6xl flex-col rounded-xl bg-neutral-900 shadow-2xl" wire:click.stop>

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-neutral-700 px-6 py-4">
                    <div>
                        <h2 class="text-xl font-semibold text-neutral-50">
                            {{ $project ? 'Edit Project' : 'New Project' }}
                        </h2>
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
                        @if($project)
                            <button
                                wire:click="setTab('stages')"
                                class="border-b-2 px-1 py-3 text-sm font-medium transition-colors {{ $activeTab === 'stages' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200' }}">
                                Stages
                                @if($project->stages->count() > 0)
                                    <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $project->stages->count() }}</span>
                                @endif
                            </button>
                            <button
                                wire:click="setTab('notes')"
                                class="border-b-2 px-1 py-3 text-sm font-medium transition-colors {{ $activeTab === 'notes' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200' }}">
                                Notes
                                @if($project->notes->count() > 0)
                                    <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $project->notes->count() }}</span>
                                @endif
                            </button>
                            <button
                                wire:click="setTab('attachments')"
                                class="border-b-2 px-1 py-3 text-sm font-medium transition-colors {{ $activeTab === 'attachments' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200' }}">
                                Attachments
                                @if($project->attachments->count() > 0)
                                    <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $project->attachments->count() }}</span>
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
                                    placeholder="Project name">
                                @error('name') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-neutral-300">Description</label>
                                <textarea
                                    wire:model="description"
                                    rows="5"
                                    class="w-full rounded-lg bg-neutral-800 px-4 py-2 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Project description"></textarea>
                                @error('description') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-3 gap-4">
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

                    <!-- Stages Tab -->
                    @if($activeTab === 'stages' && $project)
                        <div class="space-y-4">
                            <!-- Add Stage -->
                            <div class="rounded-lg bg-neutral-800/50 p-4">
                                <h4 class="mb-3 text-sm font-semibold text-neutral-300">Add New Stage</h4>
                                <div class="flex gap-2">
                                    <input
                                        wire:model="newStageName"
                                        wire:keydown.enter="createStage"
                                        type="text"
                                        class="flex-1 rounded-lg bg-neutral-800 px-4 py-2 text-sm text-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Stage name...">
                                    <button
                                        wire:click="createStage"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                        Add Stage
                                    </button>
                                </div>
                            </div>

                            <!-- Stages List -->
                            <div class="space-y-2">
                                @forelse($project->stages as $stage)
                                    <div class="rounded-lg bg-neutral-800/50 p-4">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <h5 class="text-base font-medium text-neutral-200">{{ $stage->name }}</h5>
                                                    <span class="rounded bg-neutral-700 px-2 py-0.5 text-xs text-neutral-400">
                                                        {{ ucfirst($stage->status) }}
                                                    </span>
                                                </div>
                                                @if($stage->description)
                                                    <p class="mt-1 text-sm text-neutral-400">{{ Str::limit($stage->description, 100) }}</p>
                                                @endif
                                                <div class="mt-2 flex items-center gap-3 text-xs text-neutral-500">
                                                    <span>{{ $stage->tasks->count() }} tasks</span>
                                                    @if($stage->responsible)
                                                        <span>• {{ $stage->responsible->name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex gap-2">
                                                <button
                                                    wire:click="$dispatch('openStageEditor', { stageId: {{ $stage->id }} })"
                                                    class="rounded p-1 text-neutral-400 hover:bg-neutral-700 hover:text-blue-400"
                                                    title="Edit stage">
                                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <button
                                                    wire:click="deleteStage({{ $stage->id }})"
                                                    wire:confirm="Are you sure? This will delete all tasks in this stage."
                                                    class="rounded p-1 text-neutral-400 hover:bg-neutral-700 hover:text-red-400"
                                                    title="Delete stage">
                                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Tasks in Stage -->
                                        @if($stage->tasks->count() > 0)
                                            <div class="mt-3 space-y-1 border-t border-neutral-700 pt-3">
                                                @foreach($stage->tasks->take(3) as $task)
                                                    <div class="flex items-center justify-between text-xs">
                                                        <span class="text-neutral-400">{{ $task->name }}</span>
                                                        <span class="rounded bg-neutral-700 px-1.5 py-0.5 text-neutral-500">{{ ucfirst($task->status) }}</span>
                                                    </div>
                                                @endforeach
                                                @if($stage->tasks->count() > 3)
                                                    <p class="text-xs text-neutral-500">+ {{ $stage->tasks->count() - 3 }} more tasks</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="py-8 text-center text-sm text-neutral-500">No stages yet</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- Notes Tab -->
                    @if($activeTab === 'notes' && $project)
                        <p class="text-sm text-neutral-400">Notes for projects - Can integrate similar component</p>
                    @endif

                    <!-- Attachments Tab -->
                    @if($activeTab === 'attachments' && $project)
                        <livewire:attachment-manager :attachable="$project" :key="'project-attachments-'.$project->id" />
                    @endif

                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between border-t border-neutral-700 px-6 py-4">
                    <div>
                        @if($project)
                            <button
                                wire:click="deleteProject"
                                wire:confirm="Are you sure? This will delete all stages and tasks in this project."
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                                Delete Project
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
