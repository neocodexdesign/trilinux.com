<div>
    @if($showModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90 p-4"
             x-data
             @keydown.escape.window="$wire.closeModal()"
             @click.self="$wire.closeModal()">

            <!-- Close button - fixed at top right -->
            <button
                wire:click="closeModal"
                class="fixed top-4 right-4 z-[70] rounded-full bg-white/20 p-3 text-white transition hover:bg-white/40"
            >
                <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="relative w-full max-w-4xl">
                <!-- Video title -->
                <h3 class="mb-3 text-lg font-medium text-white truncate text-center">{{ $videoTitle }}</h3>

                <!-- Video player (same as Babyflow) -->
                <video
                    src="{{ $videoUrl }}"
                    controls
                    autoplay
                    playsinline
                    class="max-w-full max-h-[80vh] rounded-lg mx-auto"
                ></video>
            </div>
        </div>
    @endif
</div>
