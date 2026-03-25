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
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full">
        <div class="min-h-screen bg-app-bg px-4 py-10 sm:px-6">
            <div class="mx-auto w-full max-w-md">
                <a href="/" class="mb-8 flex items-center justify-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/20 ring-1 ring-inset ring-primary/30">
                        <x-application-logo class="h-5 w-5 fill-current text-primary" />
                    </div>
                    <span class="text-sm font-semibold tracking-wide text-slate-100">LoreSync</span>
                </a>

                <div class="rounded-xl border border-border bg-surface/60 p-6 shadow-sm">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs text-slate-500">{{ __('Dark mode first • Minimal RPG-inspired SaaS UI') }}</p>
            </div>
        </div>
    </body>
</html>
