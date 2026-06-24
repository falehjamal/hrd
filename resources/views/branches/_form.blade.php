<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="code">Kode Cabang</label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $branch->code ?? '') }}" required />
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="name">Nama Cabang</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $branch->name ?? '') }}" required />
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="address">Alamat</label>
        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $branch->address ?? '') }}" />
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="city">Kota</label>
        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $branch->city ?? '') }}" />
        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="phone">Telepon</label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $branch->phone ?? '') }}" />
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 d-flex flex-wrap gap-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $branch->is_active ?? true) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_head_office" name="is_head_office" value="1" {{ old('is_head_office', $branch->is_head_office ?? false) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_head_office">Kantor Pusat</label>
        </div>
    </div>
</div>
