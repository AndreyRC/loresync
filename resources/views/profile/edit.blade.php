<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-user text-sm text-interactive"></i>
            <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('Profile') }}</h1>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-2xl space-y-6">
        <x-card class="p-4 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-card>

        <x-card class="p-4 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-card>

        <x-card class="p-4 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-card>
    </div>
</x-app-layout>
