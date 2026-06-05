@extends('layouts.app')

@section('title', $employee->name)

@section('content')
@include('partials.alerts')

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card card-modern h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Profil Karyawan</h5>
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Edit</a>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8">{{ $employee->employee_code }}</dd>
                    <dt class="col-sm-4">Nama</dt>
                    <dd class="col-sm-8">{{ $employee->name }}</dd>
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $employee->email ?? '-' }}</dd>
                    <dt class="col-sm-4">Telepon</dt>
                    <dd class="col-sm-8">{{ $employee->phone ?? '-' }}</dd>
                    <dt class="col-sm-4">Departemen</dt>
                    <dd class="col-sm-8">{{ $employee->department ?? '-' }}</dd>
                    <dt class="col-sm-4">Jabatan</dt>
                    <dd class="col-sm-8">{{ $employee->position ?? '-' }}</dd>
                    <dt class="col-sm-4">Shift Default</dt>
                    <dd class="col-sm-8">{{ $employee->shift ? $employee->shift->code.' - '.$employee->shift->name : '-' }}</dd>
                    <dt class="col-sm-4">Bergabung</dt>
                    <dd class="col-sm-8">{{ $employee->join_date?->format('d/m/Y') ?? '-' }}</dd>
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        @if ($employee->status === 'active')
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Nonaktif</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-4">
        <div class="card card-modern mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pola Shift Mingguan</h5>
                <a href="{{ route('employees.weekly-shifts.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Atur Pola</a>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach (\App\Models\EmployeeWeeklyShift::DAY_LABELS as $day => $label)
                        @php
                            $shiftId = $weeklyShifts[$day] ?? null;
                            $weeklyRow = $employee->weeklyShifts->firstWhere('day_of_week', $day);
                        @endphp
                        <span class="badge bg-label-primary" title="{{ $label }}">
                            {{ $label }}:
                            @if ($weeklyRow?->shift)
                                {{ $weeklyRow->shift->code }}
                            @else
                                <span class="text-muted">default</span>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card card-modern mb-4">
            <div class="card-header"><h5 class="mb-0">Akun Login</h5></div>
            <div class="card-body">
                @if ($employee->user)
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nama Akun</dt>
                        <dd class="col-sm-8">{{ $employee->user->name }}</dd>
                        <dt class="col-sm-4">Username</dt>
                        <dd class="col-sm-8">{{ $employee->user->username }}</dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $employee->user->email }}</dd>
                    </dl>
                    <p class="small text-muted mb-0 mt-2">Karyawan dapat login dan menggunakan menu <strong>Absen Saya</strong>.</p>
                @else
                    <p class="text-muted small">Buat akun agar karyawan dapat absen GPS via web.</p>
                    <form action="{{ route('employees.account.store', $employee) }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label" for="account_email">Email Login</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="account_email" name="email" value="{{ old('email', $employee->email) }}" required />
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="account_username">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="account_username" name="username" value="{{ old('username', strtolower($employee->employee_code)) }}" required />
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="account_password">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="account_password" name="password" required />
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="account_password_confirmation">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="account_password_confirmation" name="password_confirmation" required />
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-sm">Buat Akun Login</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <x-datatable-card tableId="employee-salaries-table" title="Riwayat Gaji">
            <x-slot:headerActions>
                <a href="{{ route('employees.salaries.create', $employee) }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Gaji
                </a>
            </x-slot:headerActions>
            <thead>
                <tr>
                    <th>Berlaku</th>
                    <th>Gaji Pokok</th>
                    <th>Tunjangan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="no-export">Aksi</th>
                </tr>
            </thead>
        </x-datatable-card>
    </div>
</div>

<a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Kembali</a>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#employee-salaries-table', {
        ajax: {
            url: '{{ route('employees.salaries.data', $employee) }}',
            data: (d) => { d.active_only = '0'; },
        },
        order: [[0, 'desc']],
        buttons: [],
        columns: [
            { data: 'effective_date_display', name: 'effective_date' },
            { data: 'basic_display', name: 'basic_salary' },
            { data: 'allowance_display', name: 'fixed_allowance' },
            { data: 'total_display', name: 'basic_salary', orderable: false, searchable: false },
            { data: 'status_badge', name: 'is_active', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
