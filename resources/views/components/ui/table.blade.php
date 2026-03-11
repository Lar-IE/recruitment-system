@props([
    'dense' => false,
])

@php
    $wrap = 'overflow-x-auto -mx-4 sm:mx-0';
    $tableBase = 'min-w-full text-sm';
    $cellPad = $dense ? 'py-2' : 'py-3';
@endphp

<div {{ $attributes->merge(['class' => $wrap]) }}>
    <table class="{{ $tableBase }}">
        {{ $slot }}
    </table>
    <style>
        /* Provide a small left/right gutter when horizontally scrolling on mobile */
        @media (max-width: 640px) {
            .-mx-4 > table { margin-left: 1rem; margin-right: 1rem; }
        }
    </style>
</div>

