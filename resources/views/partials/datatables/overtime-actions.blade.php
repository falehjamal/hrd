<div class="btn-action-group">
    @if ($overtime->status === \App\Models\OvertimeRequest::STATUS_PENDING)
        @if (!auth()->user()->employee)
            <form action="{{ route('overtime-requests.approve', $overtime) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm btn-icon-modern btn-outline-success" title="Setujui">
                    <i class="bx bx-check"></i>
                </button>
            </form>
            <button type="button" class="btn btn-sm btn-icon-modern btn-outline-warning" title="Tolak"
                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $overtime->id }}">
                <i class="bx bx-x"></i>
            </button>
        @else
            <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
                data-delete-url="{{ route('overtime-requests.destroy', $overtime) }}"
                data-delete-message="Hapus pengajuan lembur ini?">
                <i class="bx bx-trash"></i>
            </button>
        @endif
    @endif
</div>

@if ($overtime->status === \App\Models\OvertimeRequest::STATUS_PENDING && !auth()->user()->employee)
<div class="modal fade" id="rejectModal{{ $overtime->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('overtime-requests.reject', $overtime) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Lembur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label" for="rejection_notes_{{ $overtime->id }}">Alasan penolakan</label>
                    <textarea class="form-control" id="rejection_notes_{{ $overtime->id }}" name="rejection_notes" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
