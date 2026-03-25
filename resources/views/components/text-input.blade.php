@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'ls-focus rounded-xl border-border bg-app-bg/60 text-slate-100 placeholder:text-slate-500 shadow-sm focus:border-interactive focus:ring-interactive/50 disabled:opacity-60']) }}>
