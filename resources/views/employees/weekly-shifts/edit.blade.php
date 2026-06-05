@extends('layouts.app')

@section('title', 'Pola Shift — '.$employee->name)

@section('content')
@include('partials.alerts')

<div class="card card-modern">
    <div class="card-header">
        <h5 class="mb-0">Pola Shift Mingguan — {{ $employee->name }}</h5>
        <p class="small text-muted mb-0">Shift default cadangan: {{ $employee->shift ? $employee->shift->code.' - '.$employee->shift->name : 'belum diatur' }}</p>
    </div>
    <div class="card-body">
        <form action="{{ route('employees.weekly-shifts.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Shift</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (\App\Models\EmployeeWeeklyShift::DAY_LABELS as $day => $label)
                            <tr>
                                <td class="fw-medium">{{ $label }}</td>
                                <td>
                                    <select class="form-select @error('shifts.'.$day) is-invalid @enderror" name="shifts[{{ $day }}]">
                                        <option value="">Pakai shift default</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}" @selected(old('shifts.'.$day, $weeklyShifts[$day] ?? '') == $shift->id)>
                                                {{ $shift->code }} - {{ $shift->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shifts.'.$day)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Simpan Pola</button>
                <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
