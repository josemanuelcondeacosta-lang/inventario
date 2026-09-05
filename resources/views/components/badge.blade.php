@props(['variant' => 'neutral'])

@php
$variants = [
    'neutral' => 'badge-neutral',
    'success' => 'badge-success',
    'warning' => 'badge-warning',
    'danger'  => 'badge-danger',
    'info'    => 'badge-info',
    'brand'   => 'badge-brand',
];
@endphp

<span {{ $attributes->merge(['class' => $variants[$variant] ?? $variants['neutral']]) }}>
    {{ $slot }}
</span>
