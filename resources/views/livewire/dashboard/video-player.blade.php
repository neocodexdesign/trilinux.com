<div>
    @if($showModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4"
             x-data
             @keydown.escape.window="$wire.closeModal()"
             @click.self="$wire.closeModal()">

            <!-- Video Card -->
            <div class="w-full max-w-4xl rounded-xl bg-neutral-900 shadow-2xl overflow-hidden">
                <!-- Card Header -->
                <div class="flex items-center justify-between bg-neutral-800 px-4 py-3">
                    <h3 class="text-sm font-medium text-white truncate flex-1 pr-4">{{ $videoTitle }}</h3>
                    <button
                        wire:click="closeModal"
                        class="flex items-center justify-center size-8 rounded-lg bg-red-600 text-white transition hover:bg-red-500"
                        title="Fechar"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Video Player -->
                <div class="bg-black">
                    <video
                        src="{{ $videoUrl }}"
                        controls
                        autoplay
                        playsinline
                        class="w-full max-h-[70vh]"
                    ></video>
                </div>
            </div>
        </div>
    @endif
</div>
