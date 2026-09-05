@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert-success']) }}>
        <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0" />
        <div>{{ $status }}</div>
    </div>
@endif
