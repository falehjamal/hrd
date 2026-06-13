<div class="btn-action-group">
    <a href="{{ route('deduction-types.edit', $type) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Edit">
        <i class="bx bx-edit-alt"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('deduction-types.destroy', $type) }}"
        data-delete-message="Hapus jenis pemotongan {{ $type->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
