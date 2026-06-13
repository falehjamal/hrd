<div class="btn-action-group">
    <a href="{{ route('deductions.edit', $deduction) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Edit">
        <i class="bx bx-edit-alt"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('deductions.destroy', $deduction) }}"
        data-delete-message="Hapus pemotongan ini?">
        <i class="bx bx-trash"></i>
    </button>
</div>
