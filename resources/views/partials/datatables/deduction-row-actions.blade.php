<div class="btn-action-group">
    <x-crud-edit-button modal-id="deductionFormModal" :url="route('deductions.show', $deduction)" />
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('deductions.destroy', $deduction) }}"
        data-delete-message="Hapus pemotongan ini?">
        <i class="bx bx-trash"></i>
    </button>
</div>
