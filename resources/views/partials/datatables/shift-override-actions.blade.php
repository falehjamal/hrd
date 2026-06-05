<div class="btn-action-group">
    <a href="{{ route('shift-overrides.edit', $override) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Edit">
        <i class="bx bx-edit-alt"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('shift-overrides.destroy', $override) }}"
        data-delete-message="Hapus override jadwal {{ $override->employee?->name }} tanggal {{ $override->date->format('d/m/Y') }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
