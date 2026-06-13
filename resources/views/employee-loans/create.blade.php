@extends('layouts.app')

@section('title', 'Catat Piutang')

@section('content')
@include('partials.alerts')

<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Form Piutang / Kasbon</h5></div>
    <div class="card-body">
        <form action="{{ route('employee-loans.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="employee_id">Karyawan</label>
                    <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(old('employee_id', request('employee_id')) == $emp->id)>{{ $emp->employee_code }} — {{ $emp->name }}</option>
                        @endforeach
                    </select>
                    @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="loan_date">Tanggal Pinjaman</label>
                    <input type="date" class="form-control @error('loan_date') is-invalid @enderror" id="loan_date" name="loan_date" value="{{ old('loan_date', today()->format('Y-m-d')) }}" required />
                    @error('loan_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="principal_amount">Nominal Pinjaman (Rp)</label>
                    <input type="number" class="form-control @error('principal_amount') is-invalid @enderror" id="principal_amount" name="principal_amount" value="{{ old('principal_amount') }}" min="1" step="1" required />
                    @error('principal_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="installment_amount">Cicilan per Bulan (Rp)</label>
                    <input type="number" class="form-control @error('installment_amount') is-invalid @enderror" id="installment_amount" name="installment_amount" value="{{ old('installment_amount') }}" min="1" step="1" required />
                    @error('installment_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Preview Cicilan</label>
                    <div class="alert alert-info py-2 mb-0" id="loan-preview">
                        <span id="preview-text">Isi nominal pinjaman dan cicilan</span>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label" for="notes">Catatan</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('employee-loans.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('datatable-scripts')
<script type="module">
    const principalField = document.getElementById('principal_amount');
    const installmentField = document.getElementById('installment_amount');
    const previewText = document.getElementById('preview-text');

    async function updatePreview() {
        const principal = principalField?.value;
        const installment = installmentField?.value;

        if (!principal || !installment) {
            previewText.textContent = 'Isi nominal pinjaman dan cicilan';
            return;
        }

        const params = new URLSearchParams({ principal_amount: principal, installment_amount: installment });
        const response = await fetch(`{{ route('employee-loans.preview') }}?${params.toString()}`);
        const data = await response.json();

        previewText.textContent = `${data.total_installments} cicilan (cicilan terakhir: Rp ${Number(data.last_installment_amount).toLocaleString('id-ID')})`;
    }

    [principalField, installmentField].forEach((el) => el?.addEventListener('input', updatePreview));
    updatePreview();
</script>
@endpush
