<div class="btn-action-group">
    <x-crud-edit-button modal-id="branchFormModal" :url="route('branches.show', $branch)" />
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('branches.destroy', $branch) }}"
        data-delete-message="Hapus cabang {{ $branch->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
