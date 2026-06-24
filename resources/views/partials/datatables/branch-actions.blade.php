<div class="btn-action-group">
    <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Edit">
        <i class="bx bx-edit-alt"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('branches.destroy', $branch) }}"
        data-delete-message="Hapus cabang {{ $branch->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
