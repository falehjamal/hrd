@extends('layouts.app')

@section('title', 'Edit Cabang')

@section('content')
<x-form-card
    title="Edit Cabang"
    :breadcrumbs="[
        ['label' => 'Data Cabang', 'url' => route('branches.index')],
        ['label' => 'Edit Cabang'],
    ]"
    back-url="{{ route('branches.index') }}"
>
    <form action="{{ route('branches.update', $branch) }}" method="POST">
        @csrf
        @method('PUT')
        @include('branches._form')
        <x-form-actions cancel-url="{{ route('branches.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
