{{-- Modal detail hari --}}
<div class="modal fade" id="shiftDayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shift-day-modal-title">Detail Hari</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="shift-day-modal-body">
                <div class="text-center text-muted py-4">Memuat…</div>
            </div>
            <div class="modal-footer" id="shift-day-modal-footer"></div>
        </div>
    </div>
</div>

{{-- Modal libur perusahaan --}}
<div class="modal fade" id="companyHolidayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kelola Libur Perusahaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="company-holiday-form" class="border rounded p-3 mb-3 bg-light">
                    <input type="hidden" id="ch-id" value="">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small mb-1" for="ch-date">Tanggal</label>
                            <input type="date" class="form-control form-control-sm" id="ch-date" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small mb-1" for="ch-name">Nama Libur</label>
                            <input type="text" class="form-control form-control-sm" id="ch-name" maxlength="150" required placeholder="Mis. Idul Fitri">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="ch-active" checked>
                                <label class="form-check-label small" for="ch-active">Aktif</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small mb-1" for="ch-notes">Catatan</label>
                            <input type="text" class="form-control form-control-sm" id="ch-notes" maxlength="1000" placeholder="Opsional">
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary" id="ch-submit-btn">Simpan</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="ch-cancel-edit">Batal Edit</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="company-holidays-tbody">
                            <tr><td colspan="4" class="text-center text-muted py-3">Memuat…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
