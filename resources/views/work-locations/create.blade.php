@extends('layouts.app')

@section('title', 'Tambah Lokasi Kerja')

@section('content')
<x-form-card
    title="Tambah Lokasi Kerja"
    :breadcrumbs="[
        ['label' => 'Lokasi Kerja', 'url' => route('work-locations.index')],
        ['label' => 'Tambah Lokasi Kerja'],
    ]"
    back-url="{{ route('work-locations.index') }}"
>
    <form action="{{ route('work-locations.store') }}" method="POST">
        @csrf
        @include('work-locations._form')
        <x-form-actions cancel-url="{{ route('work-locations.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
