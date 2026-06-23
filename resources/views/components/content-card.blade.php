@props([
    'title' => null,
    'subtitle' => null,
    'breadcrumbs' => [],
])

@php
    $hasHeader = $title || $subtitle || isset($actions) || count($breadcrumbs);
@endphp

@if ($hasHeader)
    <x-page-header :title="$title" :subtitle="$subtitle" :breadcrumbs="$breadcrumbs">
        @isset($actions)
            <x-slot:actions>{{ $actions }}</x-slot:actions>
        @endisset
    </x-page-header>
@endif

<div {{ $attributes->merge(['class' => 'card card-modern content-card']) }}>
    @if ($title && isset($headerActions))
        <div class="card-header content-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="content-card-title mb-0">{{ $title }}</h5>
                @if ($subtitle)
                    <p class="content-card-subtitle mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="d-flex gap-2 flex-wrap">{{ $headerActions }}</div>
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
