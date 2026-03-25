<button {{ $attributes->merge(['type' => 'button', 'class' => 'ls-focus inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface/70 disabled:opacity-60']) }}>
    {{ $slot }}
</button>
