<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Campaigns') }}
            </h2>

            <a href="{{ route('campaigns.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('New Campaign') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{ showHelp: false }">
                    <button type="button" class="text-sm underline" @click="showHelp = !showHelp">
                        <span x-show="!showHelp">{{ __('Show help') }}</span>
                        <span x-show="showHelp">{{ __('Hide help') }}</span>
                    </button>

                    <div class="mt-3 text-sm text-gray-600" x-show="showHelp">
                        {{ __('Create a campaign to start organizing sessions, NPCs, locations, and maps.') }}
                    </div>

                    <div class="mt-6">
                        @if ($campaigns->isEmpty())
                            <p class="text-gray-600">{{ __('No campaigns yet.') }}</p>
                        @else
                            <ul class="space-y-3">
                                @foreach ($campaigns as $campaign)
                                    <li class="border rounded p-4">
                                        <div class="font-semibold">{{ $campaign->name }}</div>
                                        @if ($campaign->description)
                                            <div class="text-sm text-gray-600 mt-1">{{ $campaign->description }}</div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
