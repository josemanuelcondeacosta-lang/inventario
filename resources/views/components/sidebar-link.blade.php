@props(['href', 'active' => false, 'icon' => null])

<a href="{{ $href }}" {{ $attributes->merge(['class' => $active ? 'sidebar-link-active' : 'sidebar-link']) }}>
    @if ($icon)
        <x-icon :name="$icon" class="h-5 w-5 shrink-0" />
    @endif
    <span>{{ $slot }}</span>
</a>
