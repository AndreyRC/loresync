<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ls-focus inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-primary-hover active:bg-primary-hover/90 disabled:opacity-60']) }}>
    {{ $slot }}
</button>
