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
                <div class="d-flex align-items-center gap-3 mb-4">
                    @if ($employee->photo_path)
                        <img src="{{ $employee->photo_url }}" alt="Foto {{ $employee->name }}" class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <span class="avatar avatar-lg rounded-circle bg-label-primary d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bx bx-user bx-lg"></i>
                        </span>
                    @endif
                    <div>
                        <h6 class="mb-0">{{ $employee->name }}</h6>
                        <small class="text-muted">{{ $employee->employee_code }}</small>
                    </div>
                </div>
                <dl class="row mb-0">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8">{{ $employee->employee_code }}</dd>
                    <dt class="col-sm-4">Nama</dt>
                    <dd class="col-sm-8">{{ $employee->name }}</dd>
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $employee->email ?? '-' }}</dd>
                    <dt class="col-sm-4">Telepon</dt>
                    <dd class="col-sm-8">{{ $employee->phone ?? '-' }}</dd>
                    <dt class="col-sm-4">NIK</dt>
                    <dd class="col-sm-8">{{ $employee->national_id ?? '-' }}</dd>
                    <dt class="col-sm-4">Jenis Kelamin</dt>
                    <dd class="col-sm-8">{{ $employee->gender_label ?? '-' }}</dd>
                    <dt class="col-sm-4">Tanggal Lahir</dt>
                    <dd class="col-sm-8">{{ $employee->birth_date?->format('d/m/Y') ?? '-' }}</dd>
                    <dt class="col-sm-4">Alamat</dt>
                    <dd class="col-sm-8">{{ $employee->address ?? '-' }}</dd>
                    <dt class="col-sm-4">Unit Organisasi</dt>
                    <dd class="col-sm-8">{{ $employee->organizationalUnit?->name ?? '-' }}</dd>
                    <dt class="col-sm-4">Jabatan</dt>
                    <dd class="col-sm-8">{{ $employee->position?->name ?? '-' }}</dd>
                    <dt class="col-sm-4">Atasan</dt>
                    <dd class="col-sm-8">
                        @if ($employee->manager)
                            <a href="{{ route('employees.show', $employee->manager) }}">{{ $employee->manager->name }}</a>
                        @else
                            -
                        @endif
                    </dd>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Akun Login</h5>
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Edit Akun</a>
            </div>
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
                    <p class="text-muted small mb-2">Karyawan belum memiliki akun login.</p>
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-primary">Buat Akun via Edit</a>
                @endif
            </div>
        </div>

        <div class="card card-modern mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Saldo Cuti {{ $leaveYear }}</h5>
                <a href="{{ route('employees.leave-balances.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Atur Kuota</a>
            </div>
            <div class="card-body">
                @if ($leaveBalances->isEmpty())
                    <p class="text-muted small mb-0">Belum ada jenis cuti aktif.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Kuota</th>
                                    <th>Terpakai</th>
                                    <th>Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leaveBalances as $balance)
                                    <tr>
                                        <td>{{ $balance->leaveType->code }} — {{ $balance->leaveType->name }}</td>
                                        <td>{{ $balance->quota_days }} hari</td>
                                        <td>{{ $balance->used_days }} hari</td>
                                        <td>{{ $balance->remaining_days }} hari</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <x-datatable-card tableId="employee-leave-requests-table" title="Riwayat Pengajuan Cuti">
            <thead>
                <tr>
                    <th>Jenis Cuti</th>
                    <th>Rentang</th>
                    <th>Total Hari</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th class="no-export">Aksi</th>
                </tr>
            </thead>
        </x-datatable-card>

        <div class="card card-modern mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pemotongan Aktif</h5>
                <a href="{{ route('employees.deductions.create', $employee) }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah
                </a>
            </div>
            <div class="card-body">
                @if ($employee->activeDeductions->isEmpty())
                    <p class="text-muted small mb-0">Belum ada pemotongan aktif.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-modern mb-2">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Nominal</th>
                                    <th>Berlaku</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employee->activeDeductions as $deduction)
                                    <tr>
                                        <td>{{ $deduction->deductionType->code }} — {{ $deduction->deductionType->name }}</td>
                                        <td>{{ format_rupiah($deduction->amount) }}</td>
                                        <td>{{ $deduction->effective_date->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="small mb-0"><strong>Total pemotongan/bulan:</strong> {{ format_rupiah($employee->total_active_deductions) }}</p>
                @endif
            </div>
        </div>

        <x-datatable-card tableId="employee-deductions-table" title="Riwayat Pemotongan">
            <x-slot:headerActions>
                <a href="{{ route('employees.deductions.create', $employee) }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Pemotongan
                </a>
            </x-slot:headerActions>
            <thead>
                <tr>
                    <th>Jenis</th>
                    <th>Berlaku</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th class="no-export">Aksi</th>
                </tr>
            </thead>
        </x-datatable-card>

        <div class="card card-modern mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Piutang Aktif</h5>
                <a href="{{ route('employee-loans.create') }}?employee_id={{ $employee->id }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> Catat Piutang
                </a>
            </div>
            <div class="card-body">
                @if ($employee->activeLoans->isEmpty())
                    <p class="text-muted small mb-0">Tidak ada piutang aktif.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-modern mb-2">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pinjaman</th>
                                    <th>Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employee->activeLoans as $loan)
                                    <tr>
                                        <td><a href="{{ route('employee-loans.show', $loan) }}">{{ $loan->loan_date->format('d/m/Y') }}</a></td>
                                        <td>{{ format_rupiah($loan->principal_amount) }}</td>
                                        <td>{{ format_rupiah($loan->remaining_amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="small mb-0"><strong>Total sisa piutang:</strong> {{ format_rupiah($employee->total_loan_remaining) }}</p>
                @endif
            </div>
        </div>

        <x-datatable-card tableId="employee-loans-table" title="Riwayat Piutang">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pinjaman</th>
                    <th>Cicilan</th>
                    <th>Sisa</th>
                    <th>Status</th>
                    <th class="no-export">Aksi</th>
                </tr>
            </thead>
        </x-datatable-card>

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
    window.initServerDataTable('#employee-leave-requests-table', {
        ajax: { url: '{{ route('employees.leave-requests.data', $employee) }}' },
        order: [[1, 'desc']],
        buttons: [],
        columns: [
            { data: 'leave_type_display', name: 'leave_type_display' },
            { data: 'date_range', name: 'start_date' },
            { data: 'total_days_display', name: 'total_days', searchable: false },
            { data: 'reason', name: 'reason' },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });

    window.initServerDataTable('#employee-deductions-table', {
        ajax: {
            url: '{{ route('employees.deductions.data', $employee) }}',
            data: (d) => { d.active_only = '0'; },
        },
        order: [[1, 'desc']],
        buttons: [],
        columns: [
            { data: 'type_display', name: 'type_display' },
            { data: 'effective_date_display', name: 'effective_date' },
            { data: 'amount_display', name: 'amount', searchable: false },
            { data: 'status_badge', name: 'is_active', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });

    window.initServerDataTable('#employee-loans-table', {
        ajax: { url: '{{ route('employees.employee-loans.data', $employee) }}' },
        order: [[0, 'desc']],
        buttons: [],
        columns: [
            { data: 'loan_date_display', name: 'loan_date' },
            { data: 'principal_display', name: 'principal_amount', searchable: false },
            { data: 'installment_display', name: 'installment_amount', searchable: false },
            { data: 'remaining_display', name: 'paid_amount', searchable: false },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });

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
