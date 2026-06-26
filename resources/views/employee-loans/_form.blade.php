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
