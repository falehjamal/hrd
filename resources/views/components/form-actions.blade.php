@props([
    'cancelUrl',
    'submitLabel' => 'Simpan',
    'submitIcon' => 'bx-save',
    'cancelLabel' => 'Batal',
])

<div {{ $attributes->merge(['class' => 'form-actions d-flex gap-2 flex-wrap']) }}>
    <button type="submit" class="btn btn-primary">
        @if ($submitIcon)
            <i class="bx {{ $submitIcon }} me-1"></i>
        @endif
        {{ $submitLabel }}
    </button>
    <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary">{{ $cancelLabel }}</a>
</div>
