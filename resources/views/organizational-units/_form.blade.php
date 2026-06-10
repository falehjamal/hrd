<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="code">Kode Unit</label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $unit?->code ?? '') }}" required />
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="name">Nama Unit</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $unit?->name ?? '') }}" required />
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="parent_id">Unit Induk</label>
        <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
            <option value="">-- Tanpa induk (root) --</option>
            @foreach ($parents as $parent)
                <option value="{{ $parent->id }}" @selected(old('parent_id', $unit?->parent_id ?? '') == $parent->id)>
                    {{ $parent->name }}
                </option>
            @endforeach
        </select>
        @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="sort_order">Urutan</label>
        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $unit?->sort_order ?? 0) }}" min="0" />
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $unit?->is_active ?? true) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
    </div>
</div>
