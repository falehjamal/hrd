<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="code">Kode</label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $leaveType->code ?? '') }}" required />
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="name">Nama Jenis Cuti</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $leaveType->name ?? '') }}" required />
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="default_quota_days">Kuota Default (hari/tahun)</label>
        <input type="number" class="form-control @error('default_quota_days') is-invalid @enderror" id="default_quota_days" name="default_quota_days" value="{{ old('default_quota_days', $leaveType->default_quota_days ?? 12) }}" min="0" max="365" required />
        @error('default_quota_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="description">Keterangan</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description', $leaveType->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_paid" name="is_paid" value="1" {{ old('is_paid', $leaveType->is_paid ?? true) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_paid">Cuti berbayar</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $leaveType->is_active ?? true) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
    </div>
</div>
