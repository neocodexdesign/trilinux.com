<div class="space-y-4">
    <!-- Upload Section -->
    <div class="rounded-lg bg-neutral-800/50 p-4">
        <h4 class="mb-3 text-sm font-semibold text-neutral-300">Upload New File</h4>

        <div class="space-y-3">
            <div>
                <label class="block cursor-pointer">
                    <div class="flex items-center justify-center rounded-lg border-2 border-dashed border-neutral-600 bg-neutral-800 px-6 py-8 transition-colors hover:border-neutral-500 hover:bg-neutral-750">
                        <div class="text-center">
                            <svg class="mx-auto size-12 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mt-2 text-sm text-neutral-400">
                                <span class="font-semibold text-blue-400">Click to upload</span> or drag and drop
                            </p>
                            <p class="mt-1 text-xs text-neutral-500">Any document type (Max 50MB)</p>
                        </div>
                    </div>
                    <input wire:model="upload" type="file" class="hidden">
                </label>
                @error('upload') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror

                @if($upload)
                    <div class="mt-2 flex items-center gap-2 rounded bg-blue-950/30 px-3 py-2">
                        <svg class="size-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="flex-1 text-sm text-neutral-300">{{ $upload->getClientOriginalName() }}</span>
                        <button wire:click="$set('upload', null)" class="text-neutral-400 hover:text-red-400">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-300">Description (optional)</label>
                <textarea
                    wire:model="description"
                    rows="2"
                    class="w-full rounded-lg bg-neutral-800 px-3 py-2 text-sm text-neutral-200 placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Add a description for this file..."></textarea>
                @error('description') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
            </div>

            <button
                wire:click="uploadFile"
                wire:loading.attr="disabled"
                class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 disabled:opacity-50">
                <span wire:loading.remove wire:target="uploadFile">Upload File</span>
                <span wire:loading wire:target="uploadFile">Uploading...</span>
            </button>
        </div>
    </div>

    <!-- Attachments List -->
    <div>
        <h4 class="mb-3 text-sm font-semibold text-neutral-300">
            Files ({{ $attachments->count() }})
        </h4>

        @forelse($attachments as $attachment)
            <div class="mb-2 rounded-lg bg-neutral-800/50 p-3">
                @if($editingAttachmentId === $attachment->id)
                    <!-- Edit Mode -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <svg class="size-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-sm font-medium text-neutral-300">{{ $attachment->filename }}</span>
                        </div>
                        <textarea
                            wire:model="editingDescription"
                            rows="2"
                            class="w-full rounded bg-neutral-800 px-3 py-2 text-sm text-neutral-200 focus:outline-none"></textarea>
                        <div class="flex gap-2">
                            <button
                                wire:click="updateAttachment"
                                class="rounded bg-blue-600 px-3 py-1 text-xs font-medium text-white hover:bg-blue-700">
                                Save
                            </button>
                            <button
                                wire:click="cancelEdit"
                                class="rounded bg-neutral-700 px-3 py-1 text-xs font-medium text-neutral-300 hover:bg-neutral-600">
                                Cancel
                            </button>
                        </div>
                    </div>
                @else
                    <!-- View Mode -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <svg class="size-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-neutral-200">{{ $attachment->filename }}</p>
                                    <div class="flex items-center gap-2 text-xs text-neutral-500">
                                        <span>{{ $attachment->getFormattedSize() }}</span>
                                        <span>•</span>
                                        <span>{{ $attachment->user->name }}</span>
                                        <span>•</span>
                                        <span>{{ $attachment->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($attachment->description)
                                        <p class="mt-1 text-xs text-neutral-400">{{ $attachment->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-1">
                            <a
                                href="{{ route('attachments.download', $attachment->id) }}"
                                class="rounded p-1 text-neutral-400 transition-colors hover:bg-neutral-700 hover:text-blue-400"
                                title="Download">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>

                            @if($attachment->user_id === auth()->id())
                                <button
                                    wire:click="editAttachment({{ $attachment->id }})"
                                    class="rounded p-1 text-neutral-400 transition-colors hover:bg-neutral-700 hover:text-blue-400"
                                    title="Edit description">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button
                                    wire:click="deleteAttachment({{ $attachment->id }})"
                                    wire:confirm="Are you sure you want to delete this file?"
                                    class="rounded p-1 text-neutral-400 transition-colors hover:bg-neutral-700 hover:text-red-400"
                                    title="Delete">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-lg bg-neutral-800/30 px-6 py-8 text-center">
                <svg class="mx-auto mb-3 size-12 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-neutral-500">No files uploaded yet</p>
            </div>
        @endforelse
    </div>
</div>
