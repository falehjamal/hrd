@extends('layouts.app')

@section('title', 'Edit Lokasi Kerja')

@section('content')
<x-form-card
    title="Edit Lokasi Kerja"
    :breadcrumbs="[
        ['label' => 'Lokasi Kerja', 'url' => route('work-locations.index')],
        ['label' => 'Edit Lokasi Kerja'],
    ]"
    back-url="{{ route('work-locations.index') }}"
>
    <form action="{{ route('work-locations.update', $workLocation) }}" method="POST">
        @csrf
        @method('PUT')
        @include('work-locations._form')
        <x-form-actions cancel-url="{{ route('work-locations.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
