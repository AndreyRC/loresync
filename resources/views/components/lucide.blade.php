@props([
    'name',
    'class' => 'h-4 w-4',
])

@php
    $stroke = $attributes->get('stroke', 'currentColor');
    $strokeWidth = $attributes->get('stroke-width', 2);

    $icons = [
        'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'search' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
        'tag' => '<path d="M20.59 13.41 11 3H4v7l9.59 9.59a2 2 0 0 0 2.82 0l4.18-4.18a2 2 0 0 0 0-2.82Z"/><path d="M7 7h.01"/>',
        'image' => '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/>',
        'upload' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>',
        'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 7L2 7"/>',
        'lock' => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        'scroll' => '<path d="M8 21h12a2 2 0 0 0 2-2V7"/><path d="M18 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2"/><path d="M8 21a2 2 0 0 1-2-2V5"/>'
    ];

    $paths = $icons[$name] ?? null;
@endphp

@if ($paths)
    <svg
        {{ $attributes->except(['name', 'stroke', 'stroke-width', 'class'])->merge(['class' => $class]) }}
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        stroke="{{ $stroke }}"
        stroke-width="{{ $strokeWidth }}"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
    >
        {!! $paths !!}
    </svg>
@endif
