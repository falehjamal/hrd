<div class="btn-action-group">
    <a href="{{ route('work-locations.edit', $loc) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Edit">
        <i class="bx bx-edit-alt"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('work-locations.destroy', $loc) }}"
        data-delete-message="Hapus lokasi {{ $loc->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
