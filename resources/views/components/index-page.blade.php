{{--
    Standar halaman index dengan DataTables.
    Slot: filters (opsional), thead via $slot
--}}
@props([
    'tableId',
    'title',
    'subtitle' => null,
    'tableTitle' => null,
    'breadcrumbs' => [],
])

@include('partials.alerts')

<x-page-header :title="$title" :subtitle="$subtitle" :breadcrumbs="$breadcrumbs">
    @isset($actions)
        <x-slot:actions>{{ $actions }}</x-slot:actions>
    @endisset
</x-page-header>

@if (isset($stats))
    <div class="row g-4 mb-4">{{ $stats }}</div>
@endif

<x-datatable-card :tableId="$tableId" :title="$tableTitle ?? ('Daftar '.$title)" :subtitle="$subtitle">
    @isset($filters)
        <x-slot:filters>{{ $filters }}</x-slot:filters>
    @endisset
    {{ $slot }}
</x-datatable-card>

@isset($footer)
    <div class="mt-4">{{ $footer }}</div>
@endisset
