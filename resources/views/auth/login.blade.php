<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <h1 class="text-xl font-semibold tracking-tight text-slate-100">Entrar</h1>
        <p class="mt-1 text-sm text-slate-400">Informe seus dados para acessar sua conta.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="space-y-4">
            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input
                    id="password"
                    class="block mt-1 w-full"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="ls-focus rounded border-border bg-app-bg/60 text-primary shadow-sm focus:ring-interactive/50" name="remember">
                    <span class="ms-2 text-sm text-slate-400">Lembrar de mim</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="ls-focus rounded-md text-sm text-slate-400 underline transition hover:text-slate-200" href="{{ route('password.request') }}">
                        Esqueceu sua senha?
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-6 space-y-3">
            <x-primary-button class="w-full justify-center">
                Entrar
            </x-primary-button>

            <a
                href="{{ route('register') }}"
                class="ls-focus w-full inline-flex items-center justify-center rounded-xl border border-border bg-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface/70"
            >
                Criar conta
            </a>
        </div>
    </form>
</x-guest-layout>
