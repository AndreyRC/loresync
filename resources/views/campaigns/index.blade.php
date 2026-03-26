<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full p-3">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-scroll text-sm text-interactive"></i>
                <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('Campaigns') }}</h1>
            </div>

            <a href="{{ route('campaigns.create') }}" class="ls-focus inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-primary-hover">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>{{ __('New Campaign') }}</span>
            </a>
        </div>
    </x-slot>

    <x-card x-data="{ showHelp: false }">
        <button type="button" class="ls-focus inline-flex items-center gap-2 text-sm text-slate-300 transition hover:text-slate-100" @click="showHelp = !showHelp">
            <i class="fa-solid fa-circle-question text-sm"></i>
            <span x-show="!showHelp">{{ __('Show help') }}</span>
            <span x-show="showHelp">{{ __('Hide help') }}</span>
        </button>

        <div class="mt-3 text-sm text-slate-400" x-show="showHelp" x-transition.opacity>
            {{ __('Create a campaign to start organizing sessions, NPCs, locations, and maps.') }}
        </div>

        <div class="mt-6">
            @if ($campaigns->isEmpty())
                <div class="rounded-xl border border-border bg-app-bg/40 p-4">
                    <p class="text-sm text-slate-200">{{ __('No campaigns yet.') }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ __('Create one to begin tracking your world, characters, and sessions.') }}</p>
                </div>
            @else
                <ul class="space-y-3">
                    @foreach ($campaigns as $campaign)
                        <li class="rounded-xl border border-border bg-app-bg/40 p-4 transition hover:bg-app-bg/50">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-sm font-semibold text-slate-100">{{ $campaign->name }}</div>
                                    @if ($campaign->description)
                                        <div class="mt-1 text-sm text-slate-400">{{ $campaign->description }}</div>
                                    @endif
                                </div>

                                <div class="pt-1 text-xs text-slate-500">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </x-card>
</x-app-layout>
