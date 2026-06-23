@extends('layouts.app')

@section('title', 'Pola Shift — '.$employee->name)

@section('content')
<x-form-card
    title="Pola Shift Mingguan"
    subtitle="Shift default cadangan: {{ $employee->shift ? $employee->shift->code.' - '.$employee->shift->name : 'belum diatur' }}"
    :breadcrumbs="[
        ['label' => 'Data Karyawan', 'url' => route('employees.index')],
        ['label' => $employee->name, 'url' => route('employees.show', $employee)],
        ['label' => 'Pola Shift'],
    ]"
    back-url="{{ route('employees.show', $employee) }}"
>
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
        <x-form-actions cancel-url="{{ route('employees.show', $employee) }}" submit-label="Simpan Pola" class="mt-4" />
    </form>
</x-form-card>
@endsection
