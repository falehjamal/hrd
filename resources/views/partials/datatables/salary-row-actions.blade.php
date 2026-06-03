<div class="btn-action-group">
    <a href="{{ route('salaries.edit', $salary) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Edit">
        <i class="bx bx-edit-alt"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('salaries.destroy', $salary) }}"
        data-delete-message="Hapus data gaji berlaku {{ $salary->effective_date->format('d/m/Y') }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
