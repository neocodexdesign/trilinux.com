<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your profile information')">
        <!-- Avatar Section -->
        <div class="my-6 pb-6 border-b border-zinc-200 dark:border-zinc-700">
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-4">{{ __('Profile Photo') }}</label>

            <div class="flex items-center gap-6">
                <!-- Current Avatar Preview -->
                <div class="relative">
                    @if($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" alt="Avatar preview" class="size-20 rounded-full object-cover ring-2 ring-indigo-500 ring-offset-2 ring-offset-white dark:ring-offset-zinc-800">
                    @elseif($currentAvatar)
                        <img src="{{ asset('storage/' . $currentAvatar) }}" alt="Avatar" class="size-20 rounded-full object-cover ring-2 ring-zinc-300 dark:ring-zinc-600 ring-offset-2 ring-offset-white dark:ring-offset-zinc-800">
                    @else
                        <div class="flex size-20 items-center justify-center rounded-full bg-zinc-200 text-2xl font-semibold text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300 ring-2 ring-zinc-300 dark:ring-zinc-600 ring-offset-2 ring-offset-white dark:ring-offset-zinc-800">
                            {{ auth()->user()->initials() }}
                        </div>
                    @endif

                    <!-- Loading indicator -->
                    <div wire:loading wire:target="avatar" class="absolute inset-0 flex items-center justify-center rounded-full bg-black/50">
                        <svg class="size-6 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Upload Controls -->
                <div class="flex flex-col gap-2">
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ __('Choose Photo') }}
                        <input type="file" wire:model="avatar" accept="image/*" class="hidden">
                    </label>

                    @if($currentAvatar)
                        <button type="button" wire:click="removeAvatar" wire:confirm="{{ __('Are you sure you want to remove your profile photo?') }}" class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 shadow-sm transition hover:bg-red-50 dark:border-red-500/50 dark:bg-zinc-800 dark:text-red-400 dark:hover:bg-red-500/10">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            {{ __('Remove') }}
                        </button>
                    @endif

                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('JPG, PNG or GIF. Max 2MB.') }}</p>
                </div>
            </div>

            @error('avatar')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            @if($avatar)
                <div class="mt-4 flex gap-2">
                    <flux:button wire:click="updateAvatar" variant="primary" size="sm">
                        {{ __('Save Photo') }}
                    </flux:button>
                    <flux:button wire:click="$set('avatar', null)" variant="ghost" size="sm">
                        {{ __('Cancel') }}
                    </flux:button>
                </div>
            @endif

            <x-action-message class="mt-2" on="avatar-updated">
                {{ __('Photo updated.') }}
            </x-action-message>
            <x-action-message class="mt-2" on="avatar-removed">
                {{ __('Photo removed.') }}
            </x-action-message>
        </div>

        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
