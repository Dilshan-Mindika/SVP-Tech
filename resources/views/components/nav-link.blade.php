@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-cyber-cyan text-sm font-medium leading-5 text-cyber-cyan focus:outline-none focus:border-cyber-cyan transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-400 hover:text-cyber-cyan hover:border-cyber-border focus:outline-none focus:text-cyber-cyan focus:border-cyber-cyan transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
