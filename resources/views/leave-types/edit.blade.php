@extends('layouts.app')

@section('title', 'Edit Jenis Cuti')

@section('content')
<x-form-card
    title="Edit Jenis Cuti"
    :breadcrumbs="[
        ['label' => 'Jenis Cuti', 'url' => route('leave-types.index')],
        ['label' => 'Edit Jenis Cuti'],
    ]"
    back-url="{{ route('leave-types.index') }}"
>
    <form action="{{ route('leave-types.update', $leaveType) }}" method="POST">
        @csrf
        @method('PUT')
        @include('leave-types._form')
        <x-form-actions cancel-url="{{ route('leave-types.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
