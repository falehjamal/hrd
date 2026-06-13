@extends('layouts.app')

@section('title', 'Ajukan Cuti')

@section('content')
@include('partials.alerts')

<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Form Pengajuan Cuti</h5></div>
    <div class="card-body">
        <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                @if ($linkedEmployee)
                    <div class="col-12">
                        <p class="mb-0 text-muted">Karyawan: <strong>{{ $linkedEmployee->employee_code }} — {{ $linkedEmployee->name }}</strong></p>
                        <input type="hidden" id="employee_id" name="employee_id" value="{{ $linkedEmployee->id }}" />
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
                <div class="col-md-6">
                    <label class="form-label" for="leave_type_id">Jenis Cuti</label>
                    <select class="form-select @error('leave_type_id') is-invalid @enderror" id="leave_type_id" name="leave_type_id" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($leaveTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('leave_type_id') == $type->id)>{{ $type->code }} — {{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('leave_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="start_date">Tanggal Mulai</label>
                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required />
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="end_date">Tanggal Selesai</label>
                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required />
                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <div class="alert alert-info py-2 mb-0" id="leave-days-preview" style="display: none;">
                        Total hari kerja: <strong id="leave-days-count">0</strong> hari
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label" for="reason">Alasan</label>
                    <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                    @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="attachment">Lampiran (opsional, PDF/JPG/PNG, max 2MB)</label>
                    <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png" />
                    @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('datatable-scripts')
<script type="module">
    const employeeField = document.getElementById('employee_id');
    const startField = document.getElementById('start_date');
    const endField = document.getElementById('end_date');
    const preview = document.getElementById('leave-days-preview');
    const countEl = document.getElementById('leave-days-count');

    async function updatePreview() {
        const employeeId = employeeField?.value;
        const startDate = startField?.value;
        const endDate = endField?.value;

        if (!employeeId || !startDate || !endDate) {
            preview.style.display = 'none';
            return;
        }

        const params = new URLSearchParams({ employee_id: employeeId, start_date: startDate, end_date: endDate });
        const response = await fetch(`{{ route('leave-requests.calculate-days') }}?${params.toString()}`);
        const data = await response.json();

        countEl.textContent = data.total_days ?? 0;
        preview.style.display = 'block';
    }

    [employeeField, startField, endField].forEach((el) => {
        el?.addEventListener('change', updatePreview);
    });

    updatePreview();
</script>
@endpush
