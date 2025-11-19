<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <!-- BARRA VERMELHA DE IDENTIFICAÇÃO -->
        <div class="fixed top-0 left-0 right-0 z-[9999] bg-gradient-to-r from-red-600 via-red-500 to-red-600 text-white text-center py-3 px-4 shadow-2xl border-b-4 border-red-700">
            <div class="flex items-center justify-center gap-3">
                <svg class="w-6 h-6 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-lg font-bold tracking-wide">✓ VERSÃO ATUALIZADA COM BOTTOM NAVIGATION - TRILINUX 2025</span>
                <svg class="w-6 h-6 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>

        <!-- Spacer para a barra vermelha -->
        <div class="h-12"></div>

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

        <!-- Bottom Navigation Bar (Mobile & Desktop) -->
        <nav class="fixed bottom-0 left-0 right-0 z-50 bg-zinc-900 border-t border-zinc-700 shadow-2xl lg:left-64">
            <div class="flex items-center justify-around h-16 px-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full gap-1 transition-colors {{ request()->routeIs('dashboard') ? 'text-indigo-400' : 'text-zinc-400 hover:text-zinc-200' }}"
                   wire:navigate>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-xs font-medium">Dashboard</span>
                </a>

                <!-- My Tasks -->
                <a href="{{ route('tasks.my.pending') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full gap-1 transition-colors {{ request()->routeIs('tasks.my.*') ? 'text-indigo-400' : 'text-zinc-400 hover:text-zinc-200' }}"
                   wire:navigate>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="text-xs font-medium">Tarefas</span>
                </a>

                <!-- Team Tasks -->
                <a href="{{ route('tasks.team.pending') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full gap-1 transition-colors {{ request()->routeIs('tasks.team.*') ? 'text-indigo-400' : 'text-zinc-400 hover:text-zinc-200' }}"
                   wire:navigate>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-xs font-medium">Equipes</span>
                </a>

                <!-- Admin Panel (Filament) -->
                @if(auth()->user()->role === 'admin' || auth()->user()->tenant_id === null)
                <a href="/admin"
                   class="flex flex-col items-center justify-center flex-1 h-full gap-1 transition-colors {{ request()->is('admin*') ? 'text-indigo-400' : 'text-zinc-400 hover:text-zinc-200' }}"
                   target="_blank">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-xs font-medium">Admin</span>
                </a>
                @endif

                <!-- Settings -->
                <a href="{{ route('settings.profile') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full gap-1 transition-colors {{ request()->routeIs('settings.*') ? 'text-indigo-400' : 'text-zinc-400 hover:text-zinc-200' }}"
                   wire:navigate>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-xs font-medium">Perfil</span>
                </a>
            </div>
        </nav>

        <!-- Spacer para compensar a altura do bottom nav -->
        <div class="h-16"></div>

        @fluxScripts
    </body>
</html>
