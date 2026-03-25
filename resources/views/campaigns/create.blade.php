<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-plus text-sm text-interactive"></i>
            <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('Create Campaign') }}</h1>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-3xl">
        <x-card>
            <form method="POST" action="{{ route('campaigns.store') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" name="description" class="ls-focus mt-1 block w-full rounded-xl border-border bg-app-bg/60 text-slate-100 placeholder:text-slate-500 shadow-sm focus:border-interactive focus:ring-interactive/50" rows="4" placeholder="{{ __('Optional: setting, tone, or high-level notes...') }}">{{ old('description') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <div class="flex items-center gap-3">
                    <x-primary-button>
                        <i class="fa-solid fa-check text-[10px]"></i>
                        <span>{{ __('Create') }}</span>
                    </x-primary-button>

                    <a href="{{ route('campaigns.index') }}" class="ls-focus inline-flex items-center gap-2 rounded-xl border border-border px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface/70">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        <span>{{ __('Cancel') }}</span>
                    </a>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
