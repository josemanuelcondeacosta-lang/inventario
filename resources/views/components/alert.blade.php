@props(['variant' => 'info'])

@php
$variants = [
    'success' => ['class' => 'alert-success', 'icon' => 'check-circle'],
    'error'   => ['class' => 'alert-error', 'icon' => 'x-circle'],
    'info'    => ['class' => 'alert-info', 'icon' => 'info-circle'],
];
$config = $variants[$variant] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => $config['class']]) }}>
    <x-icon :name="$config['icon']" class="mt-0.5 h-5 w-5 shrink-0" />
    <div>{{ $slot }}</div>
</div>
