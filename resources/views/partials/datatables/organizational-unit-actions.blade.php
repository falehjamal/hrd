<div class="btn-action-group">
    <a href="{{ route('organizational-units.edit', $unit) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Edit">
        <i class="bx bx-edit-alt"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('organizational-units.destroy', $unit) }}"
        data-delete-message="Hapus unit {{ $unit->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
