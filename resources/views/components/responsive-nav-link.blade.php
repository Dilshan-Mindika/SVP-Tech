@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-cyber-cyan text-start text-base font-medium text-cyber-cyan bg-cyan-500/10 focus:outline-none focus:text-cyber-cyan focus:bg-cyan-500/20 focus:border-cyber-cyan transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-400 hover:text-cyber-cyan hover:bg-cyber-card hover:border-cyber-border focus:outline-none focus:text-cyber-cyan focus:bg-cyber-card focus:border-cyber-border transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
