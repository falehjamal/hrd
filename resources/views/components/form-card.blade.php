@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
    'backUrl' => null,
    'backLabel' => 'Kembali',
])

@include('partials.alerts')

<x-page-header :title="$title" :subtitle="$subtitle" :breadcrumbs="$breadcrumbs">
    @if ($backUrl)
        <x-slot:actions>
            <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> {{ $backLabel }}
            </a>
        </x-slot:actions>
    @elseif (isset($actions))
        <x-slot:actions>{{ $actions }}</x-slot:actions>
    @endif
</x-page-header>

<div {{ $attributes->merge(['class' => 'card card-modern form-card']) }}>
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
