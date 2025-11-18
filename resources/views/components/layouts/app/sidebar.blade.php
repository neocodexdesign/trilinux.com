<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group expandable :heading="__('My Tasks')" icon="user" class="grid">
                    <flux:navlist.item icon="clock" :href="route('tasks.my.pending')" :current="request()->routeIs('tasks.my.pending')" wire:navigate>
                        {{ __('Pending') }}
                        <x-task-counter type="my" status="pending" />
                    </flux:navlist.item>
                    <flux:navlist.item icon="bolt" :href="route('tasks.my.active')" :current="request()->routeIs('tasks.my.active')" wire:navigate>
                        {{ __('Active') }}
                        <x-task-counter type="my" status="active" />
                    </flux:navlist.item>
                    <flux:navlist.item icon="pause" :href="route('tasks.my.paused')" :current="request()->routeIs('tasks.my.paused')" wire:navigate>
                        {{ __('Paused') }}
                        <x-task-counter type="my" status="paused" />
                    </flux:navlist.item>
                    <flux:navlist.item icon="check-circle" :href="route('tasks.my.completed')" :current="request()->routeIs('tasks.my.completed')" wire:navigate>
                        {{ __('Completed') }}
                        <x-task-counter type="my" status="completed" />
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group expandable :heading="__('Team Tasks')" icon="users" class="grid">
                    @foreach(auth()->user()->teams()->active()->get() as $team)
                        <flux:navlist.item icon="users" wire:click="$dispatch('selectTeam', { teamId: {{ $team->id }} })" :current="false">
                            {{ $team->name }}
                        </flux:navlist.item>
                    @endforeach
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <!-- Toast Container -->
        <div x-data="toastManager()" @toast-show.window="addToast($event.detail)" class="pointer-events-none fixed inset-0 z-[9999] flex items-end justify-end p-4">
            <div class="flex flex-col gap-3">
                <template x-for="toast in toasts" :key="toast.id">
                    <div x-show="toast.visible"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-full"
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform translate-x-0"
                         x-transition:leave-end="opacity-0 transform translate-x-full"
                         class="pointer-events-auto flex items-center gap-3 rounded-lg px-4 py-3 shadow-2xl min-w-[320px] max-w-md text-white"
                         :style="{
                             background: toast.variant === 'success' ? 'linear-gradient(to right, #22c55e, #16a34a)' :
                                        toast.variant === 'danger' ? 'linear-gradient(to right, #ef4444, #dc2626)' :
                                        toast.variant === 'info' ? 'linear-gradient(to right, #3b82f6, #2563eb)' :
                                        'linear-gradient(to right, #1f2937, #111827)',
                             border: toast.variant === 'success' ? '2px solid #4ade80' :
                                    toast.variant === 'danger' ? '2px solid #f87171' :
                                    toast.variant === 'info' ? '2px solid #60a5fa' :
                                    '2px solid #4b5563'
                         }">
                        <!-- Ícone -->
                        <div class="flex-shrink-0">
                            <svg x-show="toast.variant === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="toast.variant === 'danger'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="toast.variant === 'info'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-sm leading-relaxed" x-text="toast.text"></p>
                        </div>
                        <button @click="removeToast(toast.id)" class="flex-shrink-0 text-white/70 hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <script>
            function toastManager() {
                return {
                    toasts: [],
                    nextId: 0,
                    addToast(detail) {
                        const id = this.nextId++;

                        // Flux dispara evento com: {duration, slots: {text}, dataset: {variant}}
                        const text = detail.slots?.text || 'Notification';
                        const variant = detail.dataset?.variant || null;
                        const duration = detail.duration || 5000;

                        const toast = {
                            id,
                            text,
                            variant,
                            visible: true
                        };

                        this.toasts.push(toast);

                        setTimeout(() => {
                            this.removeToast(id);
                        }, duration);
                    },
                    removeToast(id) {
                        const index = this.toasts.findIndex(t => t.id === id);
                        if (index !== -1) {
                            this.toasts[index].visible = false;
                            setTimeout(() => {
                                this.toasts.splice(index, 1);
                            }, 300);
                        }
                    }
                }
            }
        </script>

        @fluxScripts
    </body>
</html>
