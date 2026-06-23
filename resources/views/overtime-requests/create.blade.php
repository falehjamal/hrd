@extends('layouts.app')

@section('title', 'Ajukan Lembur')

@section('content')
<x-form-card
    title="Ajukan Lembur"
    subtitle="Form pengajuan lembur"
    :breadcrumbs="[
        ['label' => 'Lembur', 'url' => route('overtime-requests.index')],
        ['label' => 'Ajukan Lembur'],
    ]"
    back-url="{{ route('overtime-requests.index') }}"
>
    <form action="{{ route('overtime-requests.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            @if ($linkedEmployee)
                <div class="col-12">
                    <p class="mb-0 text-muted">Karyawan: <strong>{{ $linkedEmployee->employee_code }} — {{ $linkedEmployee->name }}</strong></p>
                </div>
            @else
                <div class="col-12">
                    <label class="form-label" for="employee_id">Karyawan</label>
                    <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->employee_code }} — {{ $emp->name }}</option>
                        @endforeach
                    </select>
                    @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endif
            <div class="col-md-4">
                <label class="form-label" for="date">Tanggal Lembur</label>
                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', today()->format('Y-m-d')) }}" required />
                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="start_time">Jam Mulai</label>
                <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}" required />
                @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="end_time">Jam Selesai</label>
                <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time') }}" required />
                @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="reason">Alasan / Pekerjaan</label>
                <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <x-form-actions cancel-url="{{ route('overtime-requests.index') }}" submit-label="Kirim Pengajuan" class="mt-4" />
    </form>
</x-form-card>
@endsection
