@props([
    'as' => 'div',
])

@php
    $base = 'flex flex-wrap items-end justify-between gap-3';
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $base]) }}>
    {{ $slot }}
</{{ $as }}>

