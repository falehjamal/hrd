import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';
import 'datatables.net-responsive-bs5';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.css';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons-bs5/css/buttons.bootstrap5.css';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import JSZip from 'jszip';

window.JSZip = JSZip;

const indonesianLanguage = {
    emptyTable: 'Tidak ada data',
    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
    infoFiltered: '(disaring dari _MAX_ total data)',
    lengthMenu: 'Tampilkan _MENU_ data',
    loadingRecords: 'Memuat...',
    processing: 'Memproses...',
    search: 'Cari:',
    zeroRecords: 'Data tidak ditemukan',
    paginate: {
        first: 'Awal',
        last: 'Akhir',
        next: '›',
        previous: '‹',
    },
};

/**
 * @param {string|HTMLElement} selector
 * @param {object} options
 */
export function initServerDataTable(selector, options = {}) {
    const defaults = {
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
        order: [],
        language: indonesianLanguage,
        layout: {
            topStart: ['pageLength', 'buttons'],
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging',
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bx bx-export"></i> Excel',
                className: 'btn btn-sm btn-outline-secondary',
                exportOptions: { columns: ':visible:not(.no-export)' },
            },
        ],
    };

    const table = new DataTable(selector, {
        ...defaults,
        ...options,
    });

    return table;
}

window.initServerDataTable = initServerDataTable;

document.addEventListener('DOMContentLoaded', () => {
    const deleteModal = document.getElementById('deleteConfirmModal');
    if (!deleteModal) {
        return;
    }

    const form = deleteModal.querySelector('form');
    const messageEl = deleteModal.querySelector('[data-delete-message]');

    document.body.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-delete-url]');
        if (!trigger) {
            return;
        }

        event.preventDefault();
        form.action = trigger.dataset.deleteUrl;
        messageEl.textContent = trigger.dataset.deleteMessage || 'Hapus data ini?';
        bootstrap.Modal.getOrCreateInstance(deleteModal).show();
    });
});
