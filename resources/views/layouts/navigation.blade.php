@php
    $linkBase = 'group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition';
    $linkActive = 'bg-primary/15 text-slate-50 ring-1 ring-inset ring-primary/30';
    $linkInactive = 'text-slate-300 hover:bg-surface/70 hover:text-slate-50';

    $subLinkBase = 'group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition';
    $subLinkActive = 'bg-primary/10 text-slate-50 ring-1 ring-inset ring-primary/20';
    $subLinkInactive = 'text-slate-300/90 hover:bg-surface/60 hover:text-slate-50';

    $navLink = function (string|array $routePattern) use ($linkBase, $linkActive, $linkInactive) {
        $patterns = is_array($routePattern) ? $routePattern : [$routePattern];

        return $linkBase.' '.(request()->routeIs(...$patterns) ? $linkActive : $linkInactive);
    };

    $subNavLink = function (string|array $routePattern) use ($subLinkBase, $subLinkActive, $subLinkInactive) {
        $patterns = is_array($routePattern) ? $routePattern : [$routePattern];

        return $subLinkBase.' '.(request()->routeIs(...$patterns) ? $subLinkActive : $subLinkInactive);
    };
@endphp

<aside
    class="fixed inset-y-0 left-0 z-50 w-64 border-r border-border bg-surface/60 backdrop-blur supports-[backdrop-filter]:bg-surface/50"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    x-transition
    style="transform: translateX(0);"
>
    <div class="flex h-16 items-center justify-between px-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary/20 ring-1 ring-inset ring-primary/30">
                <x-application-logo class="h-5 w-5 fill-current text-primary" />
            </div>
            <div class="leading-tight">
                <div class="text-sm font-semibold text-slate-100">LoreSync</div>
                <div class="text-xs text-slate-400">Campaign toolkit</div>
            </div>
        </a>

        <button type="button" class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-surface/60 p-2 text-slate-200 transition hover:bg-surface sm:hidden" @click="sidebarOpen = false">
            <span class="sr-only">Close sidebar</span>
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>

    <nav class="px-3">
        <div class="space-y-1">
            <a href="{{ route('dashboard') }}" class="{{ $navLink('dashboard') }}">
                <i class="fa-solid fa-table-columns text-sm text-slate-300 transition group-hover:text-interactive"></i>
                <span>{{ __('Dashboard') }}</span>
            </a>

            <a href="{{ route('campaigns.index') }}" class="{{ $navLink('campaigns.*') }}">
                <i class="fa-solid fa-scroll text-sm text-slate-300 transition group-hover:text-interactive"></i>
                <span>{{ __('Campaigns') }}</span>
            </a>

            <a href="{{ route('sessions.index') }}" class="{{ $navLink('sessions.*') }}">
                <i class="fa-solid fa-calendar-days text-sm text-slate-300 transition group-hover:text-interactive"></i>
                <span>{{ __('Sessions') }}</span>
            </a>

            <a href="{{ route('maps.index') }}" class="{{ $navLink('maps.*') }}">
                <i class="fa-solid fa-map text-sm text-slate-300 transition group-hover:text-interactive"></i>
                <span>{{ __('Maps') }}</span>
            </a>

            <div
                x-data="{ open: {{ (request()->routeIs('world-build.*') || request()->routeIs('characters.*') || request()->routeIs('locations.*') || request()->routeIs('items.*') || request()->routeIs('abilities.*')) ? 'true' : 'false' }} }"
                class="space-y-1"
            >
                <button type="button" class="{{ $navLink(['world-build.*', 'characters.*', 'locations.*', 'items.*', 'abilities.*']) }} w-full" @click="open = !open">
                    <i class="fa-solid fa-hammer text-sm text-slate-300 transition group-hover:text-interactive"></i>
                    <span class="flex-1 text-left">{{ __('Wold Build') }}</span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div class="ml-6 space-y-1" x-show="open" style="display: none;">
                    <a href="{{ route('characters.index') }}" class="{{ $subNavLink('characters.*') }}">
                        <i class="fa-solid fa-user-group text-sm text-slate-300/90 transition group-hover:text-interactive"></i>
                        <span>{{ __('Characters') }}</span>
                    </a>

                    <a href="{{ route('locations.index') }}" class="{{ $subNavLink('locations.*') }}">
                        <i class="fa-solid fa-location-dot text-sm text-slate-300/90 transition group-hover:text-interactive"></i>
                        <span>{{ __('Lugares') }}</span>
                    </a>

                    <a href="{{ route('items.index') }}" class="{{ $subNavLink('items.*') }}">
                        <i class="fa-solid fa-box text-sm text-slate-300/90 transition group-hover:text-interactive"></i>
                        <span>{{ __('Itens') }}</span>
                    </a>

                    <a href="{{ route('abilities.index') }}" class="{{ $subNavLink('abilities.*') }}">
                        <i class="fa-solid fa-bolt text-sm text-slate-300/90 transition group-hover:text-interactive"></i>
                        <span>{{ __('Habilidades') }}</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="bottom-0 left-0 right-0 border-t border-border/70 px-4 py-4">
            <div class="text-xs text-slate-400">
                <div class="font-medium text-slate-300">{{ Auth::user()->email ?? '' }}</div>
                <div class="mt-1">{{ __('Signed in') }}</div>
            </div>
        </div>
    </nav>

</aside>
