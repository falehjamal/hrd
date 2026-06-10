<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="code">Kode Jabatan</label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $position->code ?? '') }}" required />
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="name">Nama Jabatan</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $position->name ?? '') }}" required />
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="level">Level</label>
        <input type="number" class="form-control @error('level') is-invalid @enderror" id="level" name="level" value="{{ old('level', $position->level ?? 1) }}" min="1" max="99" required />
        @error('level')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Semakin kecil angka, semakin tinggi level jabatan.</div>
    </div>
    <div class="col-12">
        <label class="form-label" for="description">Keterangan</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description', $position->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $position->is_active ?? true) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
    </div>
</div>
