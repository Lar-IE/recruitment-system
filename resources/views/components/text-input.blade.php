@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'block w-full rounded-lg border-gray-300 bg-white shadow-sm placeholder:text-gray-400 '.
            'focus:border-indigo-500 focus:ring-indigo-500 '.
            'disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed',
    ]) }}
>
