/**
 * Modal CRUD generik — create/edit dari halaman index.
 */
(function () {
    const modals = document.querySelectorAll('[data-crud-modal]');

    if (!modals.length) {
        return;
    }

    const fillFormFromRecord = (form, record) => {
        form.querySelectorAll('input, select, textarea').forEach((el) => {
            const name = el.name;
            if (!name || name === '_token' || name === '_method') {
                return;
            }

            const val = record[name];

            if (el.type === 'checkbox') {
                el.checked = val === true || val === 1 || val === '1';
            } else if (el.type === 'radio') {
                el.checked = String(el.value) === String(val ?? '');
            } else if (el.tagName === 'SELECT') {
                el.value = val === null || val === undefined ? '' : String(val);
            } else if (el.type === 'date' && val) {
                el.value = String(val).substring(0, 10);
            } else if (el.type === 'time' && val) {
                el.value = String(val).substring(0, 5);
            } else if (el.type === 'file') {
                el.value = '';
            } else {
                el.value = val === null || val === undefined ? '' : val;
            }
        });

        form.dispatchEvent(new CustomEvent('crud-form:filled', { detail: { record } }));
    };

    const clearValidation = (form) => {
        form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach((el) => el.remove());
    };

    const initModal = (modalEl) => {
        const form = modalEl.querySelector('[data-crud-form]');
        if (!form) {
            return;
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const titleEl = modalEl.querySelector('.crud-form-modal__title');
        const subtitleEl = modalEl.querySelector('.crud-form-modal__subtitle');
        const methodInput = form.querySelector('.crud-form-method');
        const submitLabel = form.querySelector('.crud-form-submit-label');
        const storeUrl = (form.dataset.storeUrl || form.action).replace(/\/$/, '');
        const updateBase = (modalEl.dataset.updateBase || storeUrl).replace(/\/$/, '');
        const resourceKey = modalEl.dataset.resourceKey;

        const setCreateMode = () => {
            clearValidation(form);
            form.action = storeUrl;
            if (methodInput) {
                methodInput.disabled = true;
                methodInput.value = 'POST';
            }
            if (titleEl) {
                titleEl.textContent = modalEl.dataset.createTitle || 'Tambah';
            }
            if (subtitleEl) {
                subtitleEl.textContent = modalEl.dataset.subtitleCreate || '';
                subtitleEl.classList.toggle('d-none', !modalEl.dataset.subtitleCreate);
            }
            if (submitLabel) {
                submitLabel.textContent = modalEl.dataset.submitCreate || 'Simpan';
            }
            form.reset();
            form.querySelectorAll('input[type="checkbox"]').forEach((el) => {
                if (el.name === 'is_active' || el.name === 'is_paid') {
                    el.checked = true;
                } else {
                    el.checked = false;
                }
            });
            form.dispatchEvent(new CustomEvent('crud-form:reset'));
        };

        const setEditMode = (record) => {
            clearValidation(form);
            form.action = `${updateBase}/${record.id}`;
            if (methodInput) {
                methodInput.disabled = false;
                methodInput.value = 'PUT';
            }
            if (titleEl) {
                titleEl.textContent = modalEl.dataset.editTitle || 'Edit';
            }
            if (subtitleEl) {
                const name = record.name || record.title || record.code || '';
                subtitleEl.textContent = name
                    ? `Perbarui data ${name}.`
                    : modalEl.dataset.subtitleEdit || '';
                subtitleEl.classList.remove('d-none');
            }
            if (submitLabel) {
                submitLabel.textContent = modalEl.dataset.submitEdit || 'Simpan Perubahan';
            }
            fillFormFromRecord(form, record);
        };

        const openEdit = async (url) => {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Gagal memuat data.');
            }

            const data = await response.json();
            const record = data[resourceKey];
            if (!record) {
                throw new Error('Data tidak ditemukan.');
            }

            setEditMode(record);
            modal.show();
        };

        modalEl._crudOpenCreate = () => {
            setCreateMode();
            modal.show();
        };

        modalEl._crudOpenEdit = openEdit;

        const hasValidationError = modalEl.dataset.validationError === '1';
        const openModal = modalEl.dataset.openModal;
        const editUrl = modalEl.dataset.editUrl;

        if (openModal === 'create') {
            if (!hasValidationError) {
                setCreateMode();
            }
            modal.show();
        } else if (editUrl) {
            if (hasValidationError) {
                if (titleEl) {
                    titleEl.textContent = modalEl.dataset.editTitle || 'Edit';
                }
                if (submitLabel) {
                    submitLabel.textContent = modalEl.dataset.submitEdit || 'Simpan Perubahan';
                }
                modal.show();
            } else {
                openEdit(editUrl).catch((err) => window.alert(err.message));
            }
        }
    };

    modals.forEach(initModal);

    document.body.addEventListener('click', (event) => {
        const createBtn = event.target.closest('[data-crud-create]');
        if (createBtn) {
            event.preventDefault();
            const target = document.getElementById(createBtn.dataset.crudCreate);
            target?._crudOpenCreate?.();

            return;
        }

        const editBtn = event.target.closest('[data-crud-edit]');
        if (editBtn?.dataset.crudEditUrl) {
            event.preventDefault();
            const targetId = editBtn.dataset.crudTarget;
            const target = targetId ? document.getElementById(targetId) : editBtn.closest('[data-crud-modal]');
            target?._crudOpenEdit?.(editBtn.dataset.crudEditUrl);
        }
    });

    document.querySelectorAll('[data-static-form-modal]').forEach((modalEl) => {
        const shouldOpen = modalEl.dataset.openOnLoad === '1' || modalEl.dataset.validationError === '1';

        if (shouldOpen) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    });
})();
