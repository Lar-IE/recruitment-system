@props([
    'as' => 'div',
    'padded' => true,
])

@php
    $base = 'bg-white border border-gray-100 rounded-xl shadow-sm';
    $padding = $padded ? 'p-4 sm:p-6' : '';
@endphp

<{{ $as }} {{ $attributes->merge(['class' => trim($base.' '.$padding)]) }}>
    {{ $slot }}
</{{ $as }}>

