@props([
    'variant' => 'ghost', // ghost|primary|danger
    'size' => 'md', // sm|md
    'label' => null, // required for accessibility
])

@php
    $sizes = [
        'sm' => 'w-8 h-8',
        'md' => 'w-9 h-9',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $variants = [
        'ghost' => 'text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus-visible:ring-indigo-500',
        'primary' => 'text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 focus-visible:ring-indigo-500',
        'danger' => 'text-red-600 hover:text-red-700 hover:bg-red-50 focus-visible:ring-red-500',
    ];
    $variantClass = $variants[$variant] ?? $variants['ghost'];
@endphp

<button
    type="{{ $attributes->get('type', 'button') }}"
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center {$sizeClass} rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 {$variantClass}"]) }}
    @if($label) aria-label="{{ $label }}" title="{{ $label }}" @endif
>
    {{ $slot }}
    @if($label)
        <span class="sr-only">{{ $label }}</span>
    @endif
</button>

