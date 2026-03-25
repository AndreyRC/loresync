<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LoreSync') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @php
            try {
                echo \Illuminate\Support\Facades\Vite::withEntryPoints(['resources/css/app.css', 'resources/js/app.js']);
            } catch (\Throwable $e) {
                // If the Vite manifest/dev server isn't available yet, render without assets.
            }
        @endphp
    </head>
    <body class="h-full">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-app-bg text-slate-100">
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/60 sm:hidden" style="display: none;" @click="sidebarOpen = false"></div>

            @include('layouts.navigation')

            <div class="sm:pl-64">
                <header class="sticky top-0 z-30 border-b border-border bg-app-bg/80 backdrop-blur">
                    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button type="button" class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-surface/50 p-2 text-slate-200 transition hover:bg-surface sm:hidden" @click="sidebarOpen = true">
                                <span class="sr-only">Open sidebar</span>
                                <i class="fa-solid fa-bars text-sm"></i>
                            </button>

                            @isset($header)
                                <div class="flex items-center gap-3">
                                    {{ $header }}
                                </div>
                            @else
                                <h1 class="text-sm font-semibold tracking-wide text-slate-200">{{ config('app.name', 'LoreSync') }}</h1>
                            @endisset
                        </div>

                        <div class="flex items-center gap-2">
                            <x-dropdown align="right" width="48" contentClasses="py-1 bg-surface border border-border">
                                <x-slot name="trigger">
                                    <button class="ls-focus inline-flex items-center gap-2 rounded-xl border border-border bg-surface/50 px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-surface">
                                        <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                                        <i class="fa-solid fa-chevron-down text-xs text-slate-300"></i>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <main class="py-6">
                    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
