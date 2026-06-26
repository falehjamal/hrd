/**
 * Kalender jadwal shift — /shift-overrides
 */
(function () {
    const grid = document.getElementById('cal-grid');
    if (!grid || !window.shiftCalendarConfig) {
        return;
    }

    const cfg = window.shiftCalendarConfig;
    const overrideModalId = cfg.overrideModalId || 'shiftOverrideFormModal';
    const overrideModal = () => document.getElementById(overrideModalId);

    const openOverrideCreate = (defaults = {}) => {
        const modalEl = overrideModal();
        modalEl?._crudOpenCreate?.();
        const form = modalEl?.querySelector('[data-crud-form]');
        if (!form) {
            return;
        }
        if (defaults.employee_id) {
            const employeeField = form.querySelector('[name="employee_id"]');
            if (employeeField) {
                employeeField.value = String(defaults.employee_id);
            }
        }
        if (defaults.date) {
            const dateField = form.querySelector('[name="date"]');
            if (dateField) {
                dateField.value = defaults.date;
            }
        }
        form.dispatchEvent(new CustomEvent('crud-form:filled', { detail: { record: defaults } }));
    };

    const overrideEditButton = (id, label = 'Edit') =>
        `<button type="button" class="btn btn-xs btn-sm btn-outline-primary" data-crud-edit data-crud-target="${overrideModalId}" data-crud-edit-url="${cfg.showOverrideUrl}/${id}">${label}</button>`;

    const overrideCreateButton = (date, employeeId = '') =>
        `<button type="button" class="btn btn-primary" data-shift-override-create data-date="${date}"${employeeId ? ` data-employee-id="${employeeId}"` : ''}>
            <i class="bx bx-plus me-1"></i> Tambah Override
        </button>`;

    const now = new Date();
    const todayIso = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;

    const state = {
        year: now.getFullYear(),
        month: now.getMonth() + 1,
        employeeId: '',
        selectedDate: null,
    };

    const els = {
        monthLabel: document.getElementById('cal-month-label'),
        legend: document.getElementById('cal-legend'),
        weekdays: document.getElementById('cal-weekdays'),
        filterEmployee: document.getElementById('cal-filter-employee'),
        dayModal: document.getElementById('shiftDayModal'),
        dayModalTitle: document.getElementById('shift-day-modal-title'),
        dayModalBody: document.getElementById('shift-day-modal-body'),
        dayModalFooter: document.getElementById('shift-day-modal-footer'),
        companyModal: document.getElementById('companyHolidayModal'),
        companyTbody: document.getElementById('company-holidays-tbody'),
        companyForm: document.getElementById('company-holiday-form'),
    };

    const headers = () => ({
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': cfg.csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
    });

    const fetchJson = async (url) => {
        const res = await fetch(url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) {
            throw new Error('Gagal memuat data.');
        }
        return res.json();
    };

    const renderLegend = (legend) => {
        if (!els.legend || !legend?.length) {
            return;
        }
        els.legend.innerHTML = legend
            .map(
                (item) =>
                    `<span class="shift-cal-legend-item"><span class="shift-cal-legend-swatch ${item.class}"></span>${item.label}</span>`
            )
            .join('');
    };

    const renderWeekdays = (headersList) => {
        if (!els.weekdays) {
            return;
        }
        els.weekdays.innerHTML = (headersList || ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'])
            .map((h) => `<div class="shift-cal-weekday">${h}</div>`)
            .join('');
    };

    const renderGrid = (data) => {
        const blanks = data.leading_blanks || 0;
        let html = '';

        for (let i = 0; i < blanks; i++) {
            html += '<div class="shift-cal-day shift-cal-day--blank"></div>';
        }

        (data.days || []).forEach((day) => {
            const isToday = day.date === todayIso;
            const classes = ['shift-cal-day', day.cell_class || '', isToday ? 'shift-cal-day--today' : '']
                .filter(Boolean)
                .join(' ');
            const badge = day.badge
                ? `<span class="shift-cal-badge">${escapeHtml(day.badge)}</span>`
                : '';
            html += `<button type="button" class="${classes}" data-date="${day.date}" aria-label="Tanggal ${day.day}">
                <span class="shift-cal-day-num">${day.day}</span>${badge}
            </button>`;
        });

        grid.innerHTML = html;

        grid.querySelectorAll('[data-date]').forEach((btn) => {
            btn.addEventListener('click', () => openDayDetail(btn.dataset.date));
        });
    };

    const loadCalendar = async () => {
        grid.innerHTML = '<div class="text-center text-muted py-5 w-100">Memuat kalender…</div>';

        const params = new URLSearchParams({
            year: state.year,
            month: state.month,
        });
        if (state.employeeId) {
            params.set('employee_id', state.employeeId);
        }

        try {
            const data = await fetchJson(`${cfg.calendarUrl}?${params}`);
            if (els.monthLabel) {
                els.monthLabel.textContent = data.month_label || '—';
            }
            renderLegend(data.legend);
            renderWeekdays(data.weekday_headers);
            renderGrid(data);
        } catch {
            grid.innerHTML = '<div class="text-center text-danger py-5 w-100">Gagal memuat kalender.</div>';
        }
    };

    const escapeHtml = (str) => {
        const div = document.createElement('div');
        div.textContent = str ?? '';
        return div.innerHTML;
    };

    const sourceLabel = (source) => {
        const map = {
            override: 'Override',
            libur: 'Libur (override)',
            libur_perusahaan: 'Libur perusahaan',
            weekly: 'Pola mingguan',
            default: 'Shift default',
        };
        return map[source] || source;
    };

    const renderOverviewDetail = (data) => {
        let html = '';

        if (data.company_holiday) {
            html += `<div class="alert alert-warning py-2 mb-3">
                <strong><i class="bx bx-building-house me-1"></i> Libur Perusahaan:</strong> ${escapeHtml(data.company_holiday.name)}
                ${data.company_holiday.notes ? `<br><small class="text-muted">${escapeHtml(data.company_holiday.notes)}</small>` : ''}
            </div>`;
        }

        html += renderEmployeeList('Libur (Override)', data.libur, 'bx-bed', 'text-secondary');
        html += renderEmployeeList('Cuti / Izin', data.cuti, 'bx-calendar-x', 'text-info');
        html += renderShiftOverrideList('Ganti Shift', data.ganti_shift);

        if (!html) {
            html = '<p class="text-muted mb-0">Tidak ada jadwal khusus pada tanggal ini.</p>';
        }

        els.dayModalBody.innerHTML = html;
        els.dayModalFooter.innerHTML = `
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            ${overrideCreateButton(data.date)}`;
    };

    const renderEmployeeList = (title, items, icon, colorClass) => {
        if (!items?.length) {
            return '';
        }
        const rows = items
            .map(
                (item) => `<li class="list-group-item d-flex justify-content-between align-items-start py-2">
                    <div>
                        <span class="fw-medium">${escapeHtml(item.employee_code)}</span> — ${escapeHtml(item.employee_name)}
                        ${item.notes ? `<br><small class="text-muted">${escapeHtml(item.notes)}</small>` : ''}
                    </div>
                    ${item.override_id ? overrideEditButton(item.override_id) : ''}
                </li>`
            )
            .join('');

        return `<h6 class="mt-3 mb-2 ${colorClass}"><i class="bx ${icon} me-1"></i> ${title} (${items.length})</h6>
            <ul class="list-group list-group-flush border rounded mb-2">${rows}</ul>`;
    };

    const renderShiftOverrideList = (title, items) => {
        if (!items?.length) {
            return '';
        }
        const rows = items
            .map(
                (item) => `<li class="list-group-item d-flex justify-content-between align-items-start py-2">
                    <div>
                        <span class="fw-medium">${escapeHtml(item.employee_code)}</span> — ${escapeHtml(item.employee_name)}
                        <br><span class="badge bg-label-primary">${escapeHtml(item.shift)}</span>
                        ${item.notes ? `<br><small class="text-muted">${escapeHtml(item.notes)}</small>` : ''}
                    </div>
                    <div>${overrideEditButton(item.override_id)}</div>
                </li>`
            )
            .join('');

        return `<h6 class="mt-3 mb-2 text-primary"><i class="bx bx-transfer me-1"></i> ${title} (${items.length})</h6>
            <ul class="list-group list-group-flush border rounded mb-2">${rows}</ul>`;
    };

    const renderEmployeeDetail = (data) => {
        const statusClass =
            data.status_label === 'Cuti'
                ? 'info'
                : data.status_label === 'Libur' || data.status_label === 'Libur Perusahaan'
                  ? 'secondary'
                  : 'success';

        let html = `<div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-label-primary fs-6">${escapeHtml(data.employee.code)}</span>
            <strong>${escapeHtml(data.employee.name)}</strong>
        </div>`;

        html += `<div class="card bg-light border-0 mb-3">
            <div class="card-body py-3">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Status Jadwal</small>
                        <span class="badge bg-label-${statusClass}">${escapeHtml(data.status_label)}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Sumber</small>
                        ${escapeHtml(sourceLabel(data.schedule_source))}
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Shift</small>
                        ${escapeHtml(data.shift_label || '—')}
                        ${data.shift_time ? `<br><small>${escapeHtml(data.shift_time)}</small>` : ''}
                    </div>
                </div>
            </div>
        </div>`;

        if (data.company_holiday) {
            html += `<div class="alert alert-warning py-2 mb-3">
                <strong>Libur Perusahaan:</strong> ${escapeHtml(data.company_holiday.name)}
            </div>`;
        }

        if (data.override) {
            html += `<p class="small text-muted mb-2">
                <i class="bx bx-edit me-1"></i> Override: ${data.override.is_day_off ? 'Libur' : escapeHtml(data.override.shift || '')}
                ${data.override.notes ? ` — ${escapeHtml(data.override.notes)}` : ''}
            </p>`;
        }

        if (data.attendance) {
            html += `<div class="border rounded p-3 mb-2">
                <h6 class="mb-2"><i class="bx bx-user-check me-1"></i> Absensi</h6>
                <span class="badge bg-label-primary me-2">${escapeHtml(data.attendance.status_label)}</span>
                ${data.attendance.check_in ? `<small>Masuk: ${escapeHtml(data.attendance.check_in)}</small>` : ''}
                ${data.attendance.check_out ? `<small class="ms-2">Pulang: ${escapeHtml(data.attendance.check_out)}</small>` : ''}
            </div>`;
        } else {
            html += '<p class="text-muted small mb-0">Belum ada data absensi.</p>';
        }

        els.dayModalBody.innerHTML = html;

        let footer = '<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>';

        if (data.override?.id) {
            footer += overrideEditButton(data.override.id, 'Edit Override');
        }

        footer += overrideCreateButton(data.date, data.employee?.id || '');

        if (data.attendance?.id) {
            footer += `<a href="${cfg.attendanceUrl}/${data.attendance.id}/edit" class="btn btn-outline-info">Lihat Absensi</a>`;
        }

        els.dayModalFooter.innerHTML = footer;
    };

    const openDayDetail = async (date) => {
        state.selectedDate = date;
        els.dayModalTitle.textContent = 'Detail Hari';
        els.dayModalBody.innerHTML = '<div class="text-center text-muted py-4">Memuat…</div>';
        els.dayModalFooter.innerHTML = '';

        const modal = window.bootstrap.Modal.getOrCreateInstance(els.dayModal);
        modal.show();

        const params = new URLSearchParams({ date });
        if (state.employeeId) {
            params.set('employee_id', state.employeeId);
        }

        try {
            const data = await fetchJson(`${cfg.dayDetailUrl}?${params}`);
            els.dayModalTitle.textContent = data.date_label || 'Detail Hari';
            if (data.mode === 'employee') {
                renderEmployeeDetail(data);
            } else {
                renderOverviewDetail(data);
            }
        } catch {
            els.dayModalBody.innerHTML = '<div class="text-center text-danger py-4">Gagal memuat detail.</div>';
        }
    };

    const resetCompanyForm = () => {
        document.getElementById('ch-id').value = '';
        document.getElementById('ch-date').value = '';
        document.getElementById('ch-name').value = '';
        document.getElementById('ch-notes').value = '';
        document.getElementById('ch-active').checked = true;
        document.getElementById('ch-submit-btn').textContent = 'Simpan';
        document.getElementById('ch-cancel-edit')?.classList.add('d-none');
    };

    const loadCompanyHolidays = async () => {
        els.companyTbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Memuat…</td></tr>';

        try {
            const res = await fetchJson(cfg.companyHolidaysUrl);
            const rows = res.data || [];

            if (!rows.length) {
                els.companyTbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Belum ada libur perusahaan.</td></tr>';
                return;
            }

            els.companyTbody.innerHTML = rows
                .map((h) => {
                    const payload = encodeURIComponent(
                        JSON.stringify({
                            id: h.id,
                            date: h.date,
                            name: h.name,
                            notes: h.notes || '',
                            is_active: h.is_active,
                        })
                    );
                    return `<tr>
                        <td>${escapeHtml(h.date_display)}</td>
                        <td>${escapeHtml(h.name)}${h.notes ? `<br><small class="text-muted">${escapeHtml(h.notes)}</small>` : ''}</td>
                        <td><span class="badge bg-label-${h.is_active ? 'success' : 'secondary'}">${h.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-sm btn-outline-primary ch-edit" data-payload="${payload}">Edit</button>
                            <button type="button" class="btn btn-sm btn-outline-danger ch-delete" data-id="${h.id}">Hapus</button>
                        </td>
                    </tr>`;
                })
                .join('');

            bindCompanyHolidayActions();
        } catch {
            els.companyTbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3">Gagal memuat data.</td></tr>';
        }
    };

    const bindCompanyHolidayActions = () => {
        els.companyTbody.querySelectorAll('.ch-edit').forEach((btn) => {
            btn.addEventListener('click', () => {
                const data = JSON.parse(decodeURIComponent(btn.dataset.payload));
                document.getElementById('ch-id').value = data.id;
                document.getElementById('ch-date').value = data.date;
                document.getElementById('ch-name').value = data.name;
                document.getElementById('ch-notes').value = data.notes || '';
                document.getElementById('ch-active').checked = !!data.is_active;
                document.getElementById('ch-submit-btn').textContent = 'Perbarui';
                document.getElementById('ch-cancel-edit')?.classList.remove('d-none');
            });
        });

        els.companyTbody.querySelectorAll('.ch-delete').forEach((btn) => {
            btn.addEventListener('click', async () => {
                if (!confirm('Hapus libur perusahaan ini?')) {
                    return;
                }
                const res = await fetch(`${cfg.companyHolidayUpdateUrl}/${btn.dataset.id}`, {
                    method: 'DELETE',
                    headers: headers(),
                });
                if (res.ok) {
                    await loadCompanyHolidays();
                    await loadCalendar();
                } else {
                    alert('Gagal menghapus libur.');
                }
            });
        });
    };

    const openCompanyModal = () => {
        resetCompanyForm();
        loadCompanyHolidays();
        window.bootstrap.Modal.getOrCreateInstance(els.companyModal).show();
    };

    els.companyForm?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = document.getElementById('ch-id').value;
        const payload = {
            date: document.getElementById('ch-date').value,
            name: document.getElementById('ch-name').value,
            notes: document.getElementById('ch-notes').value || null,
            is_active: document.getElementById('ch-active').checked,
        };

        const url = id ? `${cfg.companyHolidayUpdateUrl}/${id}` : cfg.companyHolidayStoreUrl;
        const method = id ? 'PUT' : 'POST';

        const res = await fetch(url, {
            method,
            headers: headers(),
            body: JSON.stringify(payload),
        });

        if (res.ok) {
            resetCompanyForm();
            await loadCompanyHolidays();
            await loadCalendar();
        } else {
            const err = await res.json().catch(() => ({}));
            alert(err.message || 'Gagal menyimpan libur perusahaan.');
        }
    });

    document.getElementById('ch-cancel-edit')?.addEventListener('click', resetCompanyForm);
    document.getElementById('btn-company-holidays')?.addEventListener('click', openCompanyModal);

    document.getElementById('cal-prev-month')?.addEventListener('click', () => {
        state.month -= 1;
        if (state.month < 1) {
            state.month = 12;
            state.year -= 1;
        }
        loadCalendar();
    });

    document.getElementById('cal-next-month')?.addEventListener('click', () => {
        state.month += 1;
        if (state.month > 12) {
            state.month = 1;
            state.year += 1;
        }
        loadCalendar();
    });

    document.getElementById('cal-today')?.addEventListener('click', () => {
        state.year = now.getFullYear();
        state.month = now.getMonth() + 1;
        loadCalendar();
    });

    const initSelect2 = () => {
        const jq = window.jQuery;
        if (!jq || !jq.fn || !jq.fn.select2) {
            els.filterEmployee?.addEventListener('change', (e) => {
                state.employeeId = e.target.value;
                loadCalendar();
            });
            return;
        }

        const ajaxConfig = {
            url: cfg.employeeSearchUrl,
            dataType: 'json',
            delay: 250,
            data: (params) => ({ q: params.term || '' }),
            processResults: (data) => ({ results: data.results || [] }),
            cache: true,
        };

        const calFilter = jq('#cal-filter-employee');
        if (calFilter.length) {
            calFilter
                .select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Semua karyawan',
                    allowClear: true,
                    width: '100%',
                    language: { searching: () => 'Mencari…', noResults: () => 'Tidak ada karyawan', inputTooShort: () => 'Ketik untuk mencari…' },
                    ajax: ajaxConfig,
                })
                .on('change', function () {
                    state.employeeId = this.value || '';
                    loadCalendar();
                });
        }

        const listFilter = jq('#filter-employee');
        if (listFilter.length) {
            listFilter
                .select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Semua karyawan',
                    allowClear: true,
                    width: '100%',
                    language: { searching: () => 'Mencari…', noResults: () => 'Tidak ada karyawan', inputTooShort: () => 'Ketik untuk mencari…' },
                    ajax: ajaxConfig,
                })
                .on('change', function () {
                    const table = jq('#shift-overrides-table');
                    if (jq.fn.DataTable && jq.fn.DataTable.isDataTable(table)) {
                        table.DataTable().ajax.reload();
                    }
                });
        }
    };

    initSelect2();
    loadCalendar();

    document.body.addEventListener('click', (event) => {
        const createBtn = event.target.closest('[data-shift-override-create]');
        if (!createBtn) {
            return;
        }

        event.preventDefault();
        window.bootstrap.Modal.getInstance(els.dayModal)?.hide();
        openOverrideCreate({
            employee_id: createBtn.dataset.employeeId || '',
            date: createBtn.dataset.date || '',
        });
    });
})();
