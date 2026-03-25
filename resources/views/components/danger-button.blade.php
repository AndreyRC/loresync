<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ls-focus inline-flex items-center justify-center gap-2 rounded-xl bg-error px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-error/90 active:bg-error/80 disabled:opacity-60']) }}>
    {{ $slot }}
</button>
