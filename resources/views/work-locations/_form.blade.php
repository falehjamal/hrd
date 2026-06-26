<div class="row g-3">
    <div class="col-12">
        <label class="form-label" for="name">Nama Lokasi</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $workLocation?->name ?? '') }}" required />
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @if (($branches ?? collect())->isNotEmpty())
    <div class="col-md-6">
        <label class="form-label" for="branch_id">Cabang</label>
        <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id">
            <option value="">-- Global / Tanpa Cabang --</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected(old('branch_id', $workLocation?->branch_id ?? '') == $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Kosongkan untuk lokasi global (fallback semua karyawan tanpa cabang).</div>
    </div>
    @endif
    <div class="col-12">
        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-use-current-gps">
            <i class="bx bx-current-location me-1"></i> Gunakan Lokasi GPS Saat Ini
        </button>
        <span id="gps-pick-status" class="small text-muted ms-2"></span>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="latitude">Latitude</label>
        <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude', $workLocation?->latitude ?? '') }}" required />
        @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="longitude">Longitude</label>
        <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude', $workLocation?->longitude ?? '') }}" required />
        @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="radius_meters">Radius Geofence (meter)</label>
        <input type="number" class="form-control @error('radius_meters') is-invalid @enderror" id="radius_meters" name="radius_meters" value="{{ old('radius_meters', $workLocation?->radius_meters ?? 100) }}" min="10" max="5000" required />
        @error('radius_meters')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 d-flex align-items-end gap-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $workLocation?->is_active ?? true) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $workLocation?->is_default ?? false) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_default">Lokasi default GPS</label>
        </div>
    </div>
</div>
