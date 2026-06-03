<div class="btn-action-group">
    <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Edit">
        <i class="bx bx-edit-alt"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('shifts.destroy', $shift) }}"
        data-delete-message="Hapus shift {{ $shift->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
