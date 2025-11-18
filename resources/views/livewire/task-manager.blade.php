<div class="bg-white rounded-xl shadow p-4 sm:p-6 space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="space-y-1">
            <h3 class="text-lg font-semibold text-gray-900">{{ $task->name }}</h3>
            <p class="text-sm text-gray-600 break-words">{{ $task->description }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $this->statusColor }}">
                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <dt class="text-sm font-medium text-gray-500">Responsible</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $task->responsible?->name ?? 'Unassigned' }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Expected Start</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $task->expected_start_at?->format('M d, Y') ?? 'Not set' }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Expected End</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $task->expected_end_at?->format('M d, Y') ?? 'Not set' }}</dd>
        </div>
    </div>

    @if($task->estimated_hours || $task->actual_hours)
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @if($task->estimated_hours)
        <div>
            <dt class="text-sm font-medium text-gray-500">Estimated Hours</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $task->estimated_hours }}h</dd>
        </div>
        @endif
        @if($task->actual_hours)
        <div>
            <dt class="text-sm font-medium text-gray-500">Actual Hours</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $task->actual_hours }}h</dd>
        </div>
        @endif
    </div>
    @endif

    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
        @can('start', $task)
            @if($task->status === 'planned')
                <button 
                    wire:click="startTask" 
                    class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-transparent px-3 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1M7 7V3a1 1 0 011-1h8a1 1 0 011 1v4M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Start Task
                </button>
            @endif
        @endcan

        @can('pause', $task)
            @if($task->status === 'in_progress')
                <button 
                    wire:click="pauseTask" 
                    class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-transparent px-3 py-2 text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:w-auto"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pause Task
                </button>
            @endif
        @endcan

        @can('resume', $task)
            @if($task->status === 'paused')
                <button 
                    wire:click="resumeTask" 
                    class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-transparent px-3 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1M7 7V3a1 1 0 011-1h8a1 1 0 011 1v4M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Resume Task
                </button>
            @endif
        @endcan

        @can('complete', $task)
            @if(in_array($task->status, ['in_progress', 'paused']))
                <button 
                    wire:click="completeTask" 
                    class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-transparent px-3 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Complete Task
                </button>
            @endif
        @endcan

        @can('review', $task)
            <div class="relative w-full sm:w-auto" x-data="{ open: false }" @click.away="open = false">
                <button 
                    @click="open = !open"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    type="button"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Review
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-cloak x-show="open" class="absolute right-0 mt-2 w-48 rounded-md bg-white shadow-lg z-10" @click="open = false">
                    <div class="py-1 text-left">
                        <button wire:click="openReviewModal('approved')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Approve</button>
                        <button wire:click="openReviewModal('paused')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pause</button>
                        <button wire:click="openReviewModal('restarted')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Restart</button>
                        <button wire:click="openReviewModal('rejected')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Reject</button>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <!-- Review Modal -->
    @if($showReviewModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Review Task: {{ ucfirst($reviewAction) }}</h3>
                
                <div class="mb-4">
                    <label for="reviewNotes" class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                    <textarea 
                        wire:model="reviewNotes" 
                        id="reviewNotes"
                        rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Add review notes..."
                    ></textarea>
                    @error('reviewNotes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button 
                        wire:click="closeReviewModal"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="submitReview"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        Submit Review
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mt-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
Alpine.data('taskManager', () => ({
    init() {
        // Initialize any Alpine.js functionality if needed
    }
}))
</script>
@endscript
